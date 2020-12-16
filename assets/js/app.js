import '../css/app.scss';

import './elements/Alert';
import './elements/Switch';

document.querySelector('#theme').addEventListener('click', (e) => {
  e.preventDefault()
  document.body.classList.toggle('dark')
})
