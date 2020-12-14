import '../css/app.scss';

import Alert from './elements/Alert';

customElements.define('alert-message', Alert)

document.querySelector('#theme').addEventListener('click', (e) => {
  e.preventDefault()
  document.body.classList.toggle('dark')
})
