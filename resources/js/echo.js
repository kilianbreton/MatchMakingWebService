import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

const reverbKey = import.meta.env.VITE_REVERB_APP_KEY;
const reverbHost = import.meta.env.VITE_REVERB_HOST;
const reverbPort = Number(import.meta.env.VITE_REVERB_PORT ?? 8080);
const reverbScheme = import.meta.env.VITE_REVERB_SCHEME ?? 'http';


if (!reverbKey || !reverbHost) {
    console.error('Missing Reverb frontend environment variables.', {
        VITE_REVERB_APP_KEY: reverbKey,
        VITE_REVERB_HOST: reverbHost,
    });
} else {
    window.Echo = new Echo({
        broadcaster: 'pusher',
        key: reverbKey,
        cluster: 'mt1',
        wsHost: reverbHost,
        wsPort: reverbPort,
        wssPort: reverbPort,
        forceTLS: reverbScheme === 'https',
        enabledTransports: ['ws', 'wss'],
        disableStats: true,
    });

    
}