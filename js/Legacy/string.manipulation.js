function tobase64(string)
{
    string = btoa(encodeURIComponent(string));
    string = string.replace(/\//g, '_').replace(/\+/g, '-');
    return string;
}

function frombase64(string)
{
    string = string.replace(/_/g, '/').replace(/-/g, '+');
    string = decodeURIComponent(atob(string));
    return string;
}