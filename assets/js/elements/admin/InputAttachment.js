export default class InputAttachment extends HTMLInputElement {

  connectedCallback() {
    const preview = this.dataset.avatar
    this.insertAdjacentHTML('afterend', `
<div class="input-attachment">
<div class="input-attachment__preview" style="background-image: url(${preview})"></div>
</div>
`)
    this.style.display = 'none'
    this.container = this.parentElement.querySelector('.input-attachment')
    this.container.addEventListener('dragenter', this.onDragEnter.bind(this))
    this.container.addEventListener('dragleave', this.onDragLeave.bind(this))
    this.container.addEventListener('dragover', this.onDragOver)
    this.container.addEventListener('drop', this.onDrop.bind(this))
    this.preview = this.container.querySelector('.input-attachment__preview')
  }

  disconnectedCallback() {

  }

  onDragEnter(e) {
    e.stopPropagation()
    e.preventDefault()
    this.container.classList.add('is-hovered')
  }

  onDragLeave(e) {
    e.stopPropagation()
    e.preventDefault()
    this.container.classList.remove('is-hovered')
  }

  onDragOver(e) {
    e.stopPropagation()
    e.preventDefault()
  }

  async onDrop(e) {
    e.stopPropagation()
    e.preventDefault()
    this.container.classList.add('is-hovered')
    const files = e.dataTransfer.files
    if (files.length === 0) return false
    const data = new FormData()
    data.append('file', files[0])
    let url = '/tr-admin/attachment'
    if (this.attachmentId !== '') {
      url = `${url}/${this.attachmentId}`
    }
    const response = await fetch(url, {
      method: 'POST',
      body: data
    })
    const responseData = await response.json()
    if (response.ok) {
      this.setAttachment(responseData)
    } else {
      console.error('error')
    }
    this.container.classList.remove('is-hovered')
  }

  setAttachment(attachment) {
    this.preview.style.backgroundImage = `url(${attachment.url})`
    this.value = attachment.id
  }

  /**
   * @return {string}
   */
  get attachmentId() {
    return this.value
  }

}

global.customElements.define('input-attachment', InputAttachment, {extends: 'input'})
