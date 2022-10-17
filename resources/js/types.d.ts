import Echo from "laravel-echo";

declare global {
    interface Window {
        Echo: Echo;
    }
}
window.Echo = Window.Echo || {};
