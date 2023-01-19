class Timer extends HTMLElement
{
    private readonly interval: number | null = null;

    public constructor() {
        super();
        this.interval = window.setInterval(() => {
            const dataIncrease = Boolean(this.getAttribute('data-increase') ?? false);
            if (parseInt(this.innerHTML, 10) > 0 || Boolean(this.getAttribute('data-negative') ?? false)) {
                if (dataIncrease) {
                    this.innerHTML = String(parseInt(this.innerHTML, 10) + 1);
                } else {
                    this.innerHTML = String(parseInt(this.innerHTML, 10) - 1);
                }
            } else {
                clearInterval(Number(this.interval));
                if (this.id === 'refresh_timer') {
                    pageRefresh();
                }
            }
        }, 1000);
    }
}
