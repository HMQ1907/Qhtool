# QH Fashion AI 🚀

## 1. Giới thiệu dự án
Dự án QH Fashion AI là công cụ hỗ trợ tạo ảnh và video thời trang bằng AI sử dụng nền tảng **EvoLink** với các model như **Nano Banana Pro** và **Kling 3.0**.
Dự án được dọn dẹp từ một source code hệ thống quản lý có sẵn, hiện tại hệ thống tập trung hoàn toàn vào vi-nghiệp vụ **AI Image / Video Generation** với cấu trúc tối giản, phân tầng chuyên nghiệp, rất dễ để mở rộng kinh doanh sau này (đóng phí, subscription).

## 2. Công nghệ nền tảng
- **Backend:** Laravel 12, PHP 8.2+
- **Frontend:** Vue 3 (Composition API), Inertia.js, TailwindCSS
- **Database:** MySQL (Chỉ sử dụng 1 kết nối duy nhất: `mysql`)
- **Queue/Background:** Laravel queue theo cấu hình hiện tại của `.env` (mặc định local có thể chạy `sync`, khi cần tách worker thì đổi sang driver phù hợp)
- **AI Service API:** EvoLink API

---

## 3. Cấu trúc thư mục trọng tâm đáng chú ý

```text
📦 QH-Fashion-AI
 ┣ 📂 app
 ┃ ┣ 📂 Http/Controllers   # ImageGenerationController (Singleton Controller) & ShowLoginController
 ┃ ┣ 📂 Jobs               # GenerateImageJob.php (Background task xử lý request tới EvoLink)
 ┃ ┣ 📂 Models             # User.php (phân role, limit_quota) & GeneratedImage.php (History)
 ┃ ┗ 📂 Services/AI        # EvoLinkImageService.php (Class Service giao tiếp với AI API)
 ┣ 📂 config
 ┃ ┗ 📜 ai.php             # Cấu hình riêng biệt cho dự án AI (API keys, AI Models)
 ┣ 📂 database/migrations  # Các bảng cốt lõi: users, generated_images, sessions, jobs, failed_jobs
 ┣ 📂 public/images
 ┃ ┣ 📂 models             # Bỏ ảnh người mẫu vào đây (Vue sẽ tự động đọc danh sách này)
 ┃ ┗ 📂 background         # Bỏ ảnh nền vào đây
 ┣ 📂 resources/js
 ┃ ┣ 📂 Pages/Auth         # Login.vue
 ┃ ┗ 📂 Pages              # ImageGenerator.vue (Trang tạo ảnh phức tạp chính của user)
 ┗ 📜 routes/web.php       # Rất gọn nhẹ, chứa các Route UI, Store và Polling Status AI
```

---

## 4. Kiến trúc luồng chạy (Business Flow)

Dự án áp dụng chia thành các tầng rõ ràng (Controller -> Service -> Job):

1. **Frontend UI (ImageGenerator.vue):**
   - User upload ảnh sản phẩm, form validate kích thước (10MB), drag-drop.
   - User chọn Người Mẫu và Background (trên bản xem trước trực quan).
   - Khi bấm "Tạo ảnh": Nút bấm chuyển sang `Đang xử lý`. Frontend sau đó sẽ gọi ngầm polling mỗi `3 giây` về API `/image-generator/{id}/status` chờ kết quả trả về liên tục.

2. **Controller Layer:**
   - Validate thông tin. **Đặc biệt: Kiểm tra `free_images_left` của user.** Hết lượt báo lỗi.
   - Lưu ảnh tạm thời xuống storage nội bộ. Thêm mới 1 dòng ghi nhận vào bảng `generated_images` (status: `pending`) và trừ 1 lượt của user.
   - Dispatch `GenerateImageJob` vào queue hiện tại. Controller lập tức kết thúc và trả về JSON cho frontend (không block HTTP đợi AI chạy).

3. **Background Job & Service:**
   - Worker nắm lấy `GenerateImageJob` đổi trạng thái DB thành `processing`.
   - Gọi lên `EvoLinkImageService`. Service này chuyển thông số UI ("quán cafe", "nữ điệu") thành prompt tiếng Anh chuyên nghiệp + URL ảnh public để feed cho EvoLink.
   - Chờ API EvoLink trả kết quả rồi tải ảnh về server của dự án.
   - Job đổi trạng thái thành `done` để màn hình User poll và hiển thị ảnh thành phẩm.
   - **Bảo hiểm rủi ro:** Nếu gặp `Exception` mạng, catch exception sẽ cập nhật lỗi (`failed`) và hoàn lại lượt cho User đó.

5. **Video cleanup (xoa sub/watermark co dinh):**
   - User upload video goc, backend luu file tam vao `storage/app/public/uploads/videos`.
   - He thong tao record `video_cleanups`, dispatch `ProcessVideoCleanupJob` va frontend polling qua `/video-cleanup/{id}/status`.
   - `VideoCleanupService` dung `ffprobe` lay kich thuoc video, sau do chay `ffmpeg delogo` vao vung subtitle/watermark duoc cau hinh bang ty le `%`.
   - Ket qua tra ra file mp4 moi trong `storage/app/public/cleaned/videos`.

---

## 5. Hướng dẫn chạy cục bộ & Mở rộng code

Dự án đã được chốt hạ và ổn định. Khi mở rộng code, bạn chỉ việc gõ thêm Route, tạo DB Migration nếu cần.

### Môi trường chạy
Yêu cầu hệ thống phải được chạy Artisan Queue liên tục (do xử lý chạy ngầm):
```bash
# 1. Refresh tải lại bộ đệm
composer dump-autoload

# 2. Xóa cài đặt CSDL ban đầu (Tạo 2 tài khoản: Admin & Normal User)
php artisan migrate:fresh --seed

# 3. Chạy worker lắng nghe Job - chạy vĩnh viễn
#   (Mỗi khi bạn sửa code ở phần \Jobs, nhớ chạy lệnh: php artisan queue:restart)
php artisan queue:listen

# 4. Dev Frontend giao diện
npm run dev
```

### Các Account để Test theo dạng Limit & Vô hạn
Sau khi chạy `--seed` như trên:
- **Tài khoản test Quyền Admin:** `admin@gmail.com` / `123456` (Vô hạn lượt tạo)
- **Tài khoản test Người Dùng Thường:** `user@gmail.com` / `123456` (1 Account được tặng kèm 3 lần tạo ảnh và 1 lần xử lý video rỗng dự tươn sau này).

### Cấu hình biến môi trường (`.env`)
```env
# Nhớ cài đặt Connection và Session sang MySQL
DB_CONNECTION=mysql
SESSION_CONNECTION=mysql

QUEUE_CONNECTION=database

# Cấu hình API EvoLink
EVOLINK_API_KEY="Lấy mã Key tại: https://evolink.ai/"
EVOLINK_IMAGE_MODEL=nano-banana-pro-beta
EVOLINK_VIDEO_MODEL=kling-v3-text-to-video
EVOLINK_IMAGE_QUALITY=2K
EVOLINK_VIDEO_DURATION=5
EVOLINK_VIDEO_ASPECT_RATIO=16:9
EVOLINK_VIDEO_QUALITY=720p
EVOLINK_VIDEO_SOUND=off

# Video cleanup
FFMPEG_BIN=ffmpeg
FFPROBE_BIN=ffprobe
VIDEO_CLEANUP_TIMEOUT=900
VIDEO_CLEANUP_LEFT_PCT=34
VIDEO_CLEANUP_TOP_PCT=86
VIDEO_CLEANUP_WIDTH_PCT=31
VIDEO_CLEANUP_HEIGHT_PCT=9
VIDEO_CLEANUP_BAND=14
```

### Supabase Storage

```env
SUPABASE_URL=https://your-project-ref.supabase.co
SUPABASE_PUBLIC_URL=https://your-project-ref.supabase.co
SUPABASE_SERVICE_ROLE_KEY=your-supabase-service-role-key
SUPABASE_STORAGE_BUCKET=QHTOOL
```

`SUPABASE_URL` lấy ở Supabase Dashboard > Project Settings > API > Project URL. `SUPABASE_SERVICE_ROLE_KEY` cũng ở trang đó, mục `service_role` secret. `SUPABASE_STORAGE_BUCKET` là tên bucket trong Storage, và phải khớp đúng từng ký tự với tên bucket thật, ví dụ `QHTOOL`.

Bucket nên để `public`, vì backend sẽ upload ảnh sản phẩm/người mẫu/background lên Supabase rồi lấy public URL để gửi sang EvoLink.
