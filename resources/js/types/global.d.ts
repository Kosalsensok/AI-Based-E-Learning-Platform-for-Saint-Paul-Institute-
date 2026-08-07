import { PageProps as InertiaPageProps } from '@inertiajs/core';
import { AxiosInstance } from 'axios';
import { Config } from 'ziggy-js';

declare global {
    interface Window {
        axios: AxiosInstance;
    }

    var route: (name?: string, params?: any, absolute?: boolean, config?: Config) => string;
}

declare module 'vue' {
    interface ComponentCustomProperties {
        route: (name?: string, params?: any, absolute?: boolean, config?: Config) => string;
    }
}

declare module '*.vue' {
    import type { DefineComponent } from 'vue';
    const component: DefineComponent<{}, {}, any>;
    export default component;
}
