export default {
    mounted(el, binding) {
        el.__ClickOutsideHandler__ = (event) => {
            if (!el.contains(event.target)) {
                binding.value(event);
            }
        };
        document.addEventListener("click", el.__ClickOutsideHandler__);
    },
    unmounted(el) {
        document.removeEventListener("click", el.__ClickOutsideHandler__);
    },
};
