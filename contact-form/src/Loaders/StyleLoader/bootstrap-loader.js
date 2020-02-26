export default function () {
  const bsStyleTag = document.createElement('link');
  bsStyleTag.setAttribute('rel', 'stylesheet');
  bsStyleTag.setAttribute('integrety', 'sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T');
  bsStyleTag.setAttribute('href', 'https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css');
  bsStyleTag.setAttribute('crossOrigin', 'anonymous');

  const bootswatchDarklyTag = document.createElement('link');
  bootswatchDarklyTag.setAttribute('rel', 'stylesheet');
  bootswatchDarklyTag.setAttribute('href', 'https://stackpath.bootstrapcdn.com/bootswatch/4.4.1/darkly/bootstrap.min.css');
  bootswatchDarklyTag.setAttribute('crossOrigin', 'anonymous');

  document.getElementsByTagName('head')[0].appendChild(bsStyleTag);
  document.getElementsByTagName('head')[0].appendChild(bootswatchDarklyTag);
}
