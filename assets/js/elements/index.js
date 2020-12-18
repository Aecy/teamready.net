import {TimeAgo} from "./TimeAgo";
import {Switch} from "./Switch";
import {Alert} from "./Alert";
import {DeleteAccount} from "./DeleteAccount";

// Custom Elements
customElements.define('alert-message', Alert)
customElements.define('input-switch', Switch, {extends: 'input'})
customElements.define('time-ago', TimeAgo)
customElements.define('delete-account', DeleteAccount)
