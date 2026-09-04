// assets/js/push-client.js
// Standalone push-notifications module - browser side.
// Talks only to /index.php?controller=push&action=... endpoints.
// Depends on window.BASE_URL, already set by header.php / admin_header.php.

const PushClient = (function () {
    const swUrl = (window.BASE_URL || '') + '/sw.js';
    const swScope = (window.BASE_URL || '') + '/';
    const endpoint = (action) => `${window.BASE_URL || ''}/index.php?controller=push&action=${action}`;

    function isSupported() {
        return 'serviceWorker' in navigator && 'PushManager' in window;
    }

    function urlBase64ToUint8Array(base64String) {
        const padding = '='.repeat((4 - (base64String.length % 4)) % 4);
        const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
        const rawData = atob(base64);
        return Uint8Array.from([...rawData].map((c) => c.charCodeAt(0)));
    }

    async function registerServiceWorker() {
        return navigator.serviceWorker.register(swUrl, { scope: swScope });
    }

    // Requests notification permission and creates (or reuses) a push
    // subscription. Does not talk to the backend - callers decide what to
    // do with the subscription (attach to a form, POST it immediately, ...).
    async function subscribe() {
        if (!isSupported()) {
            throw new Error('הדפדפן אינו תומך בהתראות פוש');
        }

        const permission = await Notification.requestPermission();
        if (permission !== 'granted') {
            throw new Error('לא ניתנה הרשאה להתראות');
        }

        const registration = await registerServiceWorker();
        await navigator.serviceWorker.ready;

        let subscription = await registration.pushManager.getSubscription();
        if (!subscription) {
            const keyResponse = await fetch(endpoint('vapidKey'));
            const { publicKey } = await keyResponse.json();

            subscription = await registration.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: urlBase64ToUint8Array(publicKey)
            });
        }

        return subscription.toJSON();
    }

    async function subscribeAndSaveForCustomer(customerId) {
        const subscription = await subscribe();
        const response = await fetch(endpoint('subscribeCustomer'), {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ customer_id: customerId, subscription })
        });
        return response.json();
    }

    async function subscribeAndSaveForAdmin() {
        const subscription = await subscribe();
        const response = await fetch(endpoint('subscribeAdmin'), {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ subscription })
        });
        return response.json();
    }

    return {
        isSupported,
        subscribe,
        subscribeAndSaveForCustomer,
        subscribeAndSaveForAdmin
    };
})();
