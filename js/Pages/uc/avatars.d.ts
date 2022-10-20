export declare class EditAvatars {
    private readonly form;
    private readonly currentAvatar;
    private readonly sidebarAvatar;
    private readonly previewAvatar;
    private readonly avatarFile;
    constructor();
    private listen;
    preview(field: HTMLInputElement): void;
    upload(): void;
    setActive(avatar: HTMLInputElement): void;
    refresh(avatar: string): void;
    addToList(avatar: string): void;
    delete(avatar: HTMLInputElement): void;
}
