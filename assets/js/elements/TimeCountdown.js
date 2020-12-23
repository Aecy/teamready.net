const DAY = 1000 * 60 * 60 * 24
const HOUR = 1000 * 60 * 60
const MINUTE = 1000 * 60

export class TimeCountdown extends HTMLElement {
  connectedCallback() {
    const timestamp = parseInt(this.getAttribute('time'), 10) * 1000
    const date = new Date(timestamp)
    this.update(date)
  }

  disconnectedCallback() {
    window.clearTimeout(this.timer)
  }

  update(date) {
    const now = new Date().getTime()
    const distance = date - now
    const days = Math.floor(distance / DAY)
    const hours = Math.floor((distance % DAY) / HOUR)
    const minutes = Math.floor((distance % HOUR) / MINUTE)
    const seconds = Math.floor((distance % MINUTE) / 1000)
    if (distance < 0) {
      this.innerHTML = ''
      return ''
    }
    let timeInterval = 1000
    if (days > 0) {
      this.innerHTML = `${days}j ${hours}h`
      timeInterval = HOUR
    } else if (hours > 0) {
      this.innerHTML = `${hours}h ${minutes}m`
      timeInterval = MINUTE
    } else {
      this.innerHTML = `${hours}m ${minutes}s`
    }
    if (distance > 0) {
      this.timer = window.setTimeout(() => {
        if (window.requestAnimationFrame) {
          window.requestAnimationFrame(() => this.update(date))
        } else {
          this.update(date)
        }
      }, timeInterval)
    }
  }
}
