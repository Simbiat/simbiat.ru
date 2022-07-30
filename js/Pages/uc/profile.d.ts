export declare class EditProfile {
    private readonly usernameForm;
    private readonly usernameSubmit;
    private readonly usernameField;
    private readonly profileForm;
    private readonly profileSubmit;
    private profileFormData;
    private timeOut;
    constructor();
    profile(auto?: boolean): void;
    profileOnChange(): void;
    usernameOnChange(): void;
    username(): void;
}
