// axios
import axios from 'axios'

var hostname = window.document.location.hostname +
	(window.document.location.port ?
		":" + window.document.location.port.toString() :
		"");
if (hostname && hostname.startsWith('shop.')) {
	hostname = hostname.replace('shop.', '');
}

export default axios.create({
  	baseURL: "//" +
  		"backend." + hostname
})
