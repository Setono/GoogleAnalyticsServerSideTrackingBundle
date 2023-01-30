((window) => {
  window.pageview = () => {
    const url = encodeURIComponent(window.location.href);

    fetch('/pageview?url=' + url)
      .then(response => response.json())
      .then(data => console.log(data));
  }
})(window);
