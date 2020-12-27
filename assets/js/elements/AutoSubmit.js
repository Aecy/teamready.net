export class AutoSubmit extends HTMLFormElement {
  connectedCallback() {
    Array.from(this.querySelectorAll('input')).forEach(e => {
      e.addEventListener('change', () => {
        this.submit();
      })
    })
  }
}
