import '../css/app.scss';

import './elements/Alert';

document.querySelector('#theme').addEventListener('click', (e) => {
  e.preventDefault()
  document.body.classList.toggle('dark')
})
