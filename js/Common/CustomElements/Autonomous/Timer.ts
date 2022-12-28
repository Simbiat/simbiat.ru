class Timer extends HTMLElement
{
    private readonly interval: number | null = null;

    constructor() {
        super();
        this.interval = window.setInterval(() => {
            if (parseInt(this.innerHTML) > 0 || Boolean(this.getAttribute('data-negative'))) {
                if (Boolean(this.getAttribute('data-increase'))) {
                    this.innerHTML = String(parseInt(this.innerHTML) + 1);
                } else {
                    this.innerHTML = String(parseInt(this.innerHTML) - 1);
                }
            } else {
                clearInterval(Number(this.interval));
                if (this.id === 'refresh_timer') {
                    window.location.href = window.location.href+'?forceReload=true';
                }
            }
        }, 1000);
    }
}
