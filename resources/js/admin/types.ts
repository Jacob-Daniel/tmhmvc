// types.ts

export type DataModuleButton = HTMLElement & {
    dataset: {
        action: string;
    };
};

export type DataModuleContainer = HTMLElement & {
    dataset: {
        id?: string;
        back?: string;
        new?: string;
        refresh?: string;
        edit?: string;
        delete?: string;
        save?: string;
        target?: string; // optional selector for save form
        [key: string]: string | undefined;
    };
};

// Extend window
declare global {
    interface Window {
        loadContent: (
            action: string,
            item?: string | null,
            delitem?: string | null,
            page?: string | null,
            condition?: string,
        ) => Promise<void>;
        saveForm: (form: HTMLFormElement) => Promise<void>;
        showMessage: (msg: string, type?: string) => void;
    }
}

export interface GalleryParams {
    page?: string;
    item?: string;
    delitem?: string;
    condition?: string;
}
