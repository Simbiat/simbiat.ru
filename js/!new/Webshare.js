//WebSahre API call
function webShare() {
    navigator.share({
        title: document.title,
        text: getMeta('og:desription') ?? getMeta('desription'),
        url: document.location,
    })
}