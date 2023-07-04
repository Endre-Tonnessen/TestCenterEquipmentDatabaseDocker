



// Script scrolls log to the bottom. Where the most recent information is.
function updateScrollToBottom() {
    var getScrollable = document.querySelectorAll('.ScrollBottom');

    getScrollable.forEach(element => {
        var elementHeight = element.scrollHeight;
        element.scrollTop = elementHeight
    });
}
window.onload = updateScrollToBottom;





