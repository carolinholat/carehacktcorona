import axios from "axios"

axios._browserWindowId = null
axios._axiosHistory = []
axios._axiosCallNr = -1
axios._lastCompletedCall = -1

axios._initBrowserWindowId = function () {
  function guid() {
    function _p8(s) {
      var p = (Math.random().toString(16) + "000000000").substr(2, 8);
      return s ? "-" + p.substr(0, 4) + "-" + p.substr(4, 4) : p;
    }
    return _p8() + _p8(true) + _p8(true) + _p8();
  }
  this._browserWindowId = guid()
}

axios._storeAndExecuteAxiosResponse = function (payload) { //payload: currentCallNr, response
  this._axiosHistory[payload.currentCallNr].response = payload.response

  for (var i = this._lastCompletedCall + 1; i < this._axiosHistory.length; i++) {
    if (this._axiosHistory[i].response != null) {
      this._lastCompletedCall = i
      this._axiosHistory[i].success(this._axiosHistory[i].response)
      this._axiosHistory[i] = {}
    } else if(this._axiosHistory[i].error != null){
      this._lastCompletedCall = i
      // keep this._axiosHistory[i] for debugging purposes
    } else {
      // TODO: clear timer
      break;
    }
  }
}

axios._checkAxiosCall = function (currentCallNr, actionUrl) {
  var self = this
  axios({
    method: 'post',
    url: actionUrl + '/ajaxController',
    data: {
      action: 'checkCall',
      params: {
        browserWindowId: this._browserWindowId,
        callNr: currentCallNr.toString()
      }
    },
    timeout: this._axiosTimeout,
    callNr: currentCallNr.toString(),
    withCredentials: true
  })
      .then(function (response) {
        if (!response.callNrAvailable == false) {
          self._storeAndExecuteAxiosResponse({
            currentCallNr: currentCallNr,
            response: response
          });
        }
      })
      .catch(function (error) {
        if (error.response.status == 404 || error.message.includes("timeout")) {
          setTimeout(function () {
            self._retryAxiosCall(currentCallNr)
          }, self._axiosTimeout * (self._axiosCallNr[currentCallNr].nrOfRetries + 1))
        }
      });
}

axios._retryAxiosCall = function (callNr) {
  if (this._axiosCallNr[callNr].nrOfRetries < this.maxRetries) {
    this._axiosCallNr[callNr].nrOfRetries++
    var self = this
    axios({
      method: this._axiosHistory[callNr].method,
      url: this._axiosHistory[callNr].url,
      data: this._axiosHistory[callNr].data,
      timeout: this._axiosTimeout,
      withCredentials: true
    })
        .then(function (response) {
          // TODO: clear timer
          self._storeAndExecuteAxiosResponse({
            currentCallNr: callNr,
            response: response
          })

        })
        .catch(function (error) {
          if (error.response.status == 404 || error.message.includes("timeout")) {
            setTimeout(function () {
              self._retryAxiosCall(callNr)
            }, self._axiosTimeout * (self._axiosCallNr[callNr].nrOfRetries + 1))
          }
        });
  }
}

// public

axios.axiosTimeout = 120000 // ms
axios.maxRetries = 3 // times

axios.axiosCall = function (payload) { //payload: url, method, data, blockGui, success

  if(this._browserWindowId === null){
    this._initBrowserWindowId()
  }

  if(payload.method === undefined || payload.method === null) {payload.method = "POST"}
  var currentCallNr = ++this._axiosCallNr;

  if(payload.data === undefined) {
    payload.data = {}
  }

  this._axiosHistory[currentCallNr] = {
    url: payload.url,
    mathod: payload.method,
    data: payload.data,
    success: payload.success,
    blockGui: payload.blockGui,
    response: null,
    error: null,
    nrOfRetries: 0
  };

  // TODO: startTimer

  if (payload.data.params === undefined) {
    payload.data.params = {};
  };

  payload.data.params.callNr = currentCallNr.toString();
  payload.data.params.browserWindowId = this._browserWindowId;


  var self = this
  axios.defaults.withCredentials = true;
  axios({
    method: payload.method,
    url: payload.url,
    data: payload.data,
    timeout: this.axiosTimeout,
    withCredentials: true
  })
      .then(function (response) {
        self._storeAndExecuteAxiosResponse({
          currentCallNr: currentCallNr,
          response: response
        })
      })
      .catch((error) => {
        if (error.response) {
          // response error
          if (error.message.includes("timeout") || error.response.request.readyState == 0) {
            // in case of timeout --> retry
            self._checkAxiosCall(currentCallNr);
          } else {
            // TODO: clear timer
          }

        } else if (error.request) {
          // request error --> abort checks / retries
          self._axiosHistory[currentCallNr].error = error.request
          // TODO: clear timer
        } else {
          // other error --> abort checks / retries
          self._axiosHistory[currentCallNr].error = error
          // TODO: clear timer
        }
      })

}

export default axios

// maybe we can change to something like this later:

//(function (axios) {

//   var privateMethod = function () {};

//   return {
//     publicMethodOne: function () {
//       // I can call `privateMethod()` you know...
//       console.log('public one', axios)
//     },
//     publicMethodTwo: function () {

//     },
//     publicMethodThree: function () {

//     }
//   };

// })();
