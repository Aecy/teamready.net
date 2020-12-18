/**
 * @property {HTMLSpanElement} switch
 */
export class DeleteAccount extends HTMLElement {

  connectedCallback () {
    this.innerHTML = `<button class="btn-danger" onclick="return this.delete()">Delete my account</button>`
  }

  disconnectedCallback () {

  }

}
