import Vue from 'vue'
import jsSHA from 'jssha/src/sha1'

Vue.prototype.$hash = (password) => {
  const hash = new jsSHA('SHA-1', 'TEXT')
  hash.update(password)
  return hash.getHash('HEX')
}
