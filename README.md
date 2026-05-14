# Stoicism & Psychology Generator

Laravel + Vue/Inertia application for creating short-form video campaigns in the Psychology & Stoicism niche.

## Stack

- Backend: Laravel 12, PHP 8.2+
- Frontend: Vue 3, Inertia.js, TailwindCSS
- Database: MySQL
- Queue: Laravel queue
- AI provider: EvoLink

## Main Flow

1. User logs in.
2. User creates a campaign at `/campaigns`.
3. The app creates the requested number of `campaign_videos`.
4. Each video is processed through queued jobs:
   - `GenerateScriptJob`: creates the short-form script with EvoLink chat completions.
   - `GenerateVoiceJob`: converts the script to an audio file.
   - `GenerateMediaJob`: starts text-to-video generation.
   - `RenderFinalVideoJob`: prepares the final video output.

## Important Files

- `routes/web.php`: active web routes.
- `app/Http/Controllers/CampaignController.php`: campaign creation and detail pages.
- `app/Jobs/Pipeline`: campaign video generation pipeline.
- `app/Models/Campaign.php`: campaign model.
- `app/Models/CampaignVideo.php`: generated video item model.
- `resources/js/Pages/Campaign/Index.vue`: campaign list and create form.
- `resources/js/Pages/Campaign/Show.vue`: campaign progress view.
- `config/evolink.php`: EvoLink API settings.

## Local Setup

```bash
composer install
npm install
php artisan migrate:fresh --seed
php artisan queue:listen
npm run dev
```

Default seeded accounts:

- `admin@gmail.com` / `123456`
- `user@gmail.com` / `123456`

## Environment

```env
DB_CONNECTION=mysql
SESSION_CONNECTION=mysql
QUEUE_CONNECTION=database

EVOLINK_API_KEY=
EVOLINK_BASE_URL=https://api.evolink.ai/v1
EVOLINK_TEXT_MODEL=deepseek-v4-pro
EVOLINK_VOICE_MODEL=qwen3-tts-vd
EVOLINK_VIDEO_MODEL=kling-v3-text-to-video
```
