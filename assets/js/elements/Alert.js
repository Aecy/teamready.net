/**
 * @property {string} type
 */
import {slideUp} from '../modules/animation'

export class Alert extends HTMLElement {
  constructor() {
    super()
    this.type = this.getAttribute('type')
    if (this.type === 'error') {
      this.type = 'danger'
    }
    const duration = this.getAttribute('duration')
    let progressBar = ''
    if (duration !== null) {
      progressBar = `<div class="alert__progress" style="animation-duration: ${duration}s">`
      window.setTimeout(() => {
        this.close()
      }, duration * 1000)
    }
    this.innerHTML = `<div class="alert alert-${this.type}">
        <svg class="icon icon-{$name}">
          <use xlink:href="/sprite.svg#${this.icon}"></use>
        </svg>
        ${this.innerHTML}
        <button class="alert-close">
          <svg class="icon icon-{$name}">
            <use xlink:href="/sprite.svg#cross"></use>
          </svg>
        </button>
        ${progressBar}
      </div>`
    this.querySelector('.alert-close').addEventListener('click', e => {
      e.preventDefault()
      this.close()
    })
  }

  close() {
    const element = this.querySelector('.alert')
    element.classList.add('out')
    window.setTimeout(async () => {
      await slideUp(element)
      this.parentElement.removeChild(this)
      this.dispatchEvent(new CustomEvent('close'))
    }, 500)
  }

  get icon() {
    if (this.type === 'danger') {
      return 'warning'
    } else if (this.type === 'success') {
      return 'check'
    }
  }
}
