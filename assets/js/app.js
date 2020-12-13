import '../css/app.scss';

document.querySelector('#switcher').addEventListener('click', (e) => {
  e.preventDefault()
  document.body.classList.toggle('dark-mode')
})
