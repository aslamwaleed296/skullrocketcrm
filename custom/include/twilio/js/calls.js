let speakerDevices;
let ringtoneDevices;
let outputVolumeBar;
let inputVolumeBar;
let volumeIndicators;
let callButton;
let outgoingCallHangupButton;
let callControlsDiv;
let audioSelectionDiv;
let getAudioDevicesButton;
let incomingCallDiv;
let incomingCallHangupButton;
let incomingCallAcceptButton;
let incomingCallRejectButton;
let incomingPhoneNumberEl;
let phoneNumberInput;
let device;
let recordWrapper;
let record;
let stop;
let soundClips;
let canvas;
let mainSection;
let audioCtx;
let canvasCtx;
const mimeType = 'audio/ogg';
// let call_stream = null;
let recorder = null;
let chunks;

function setupClient() {
    SUGAR.ajaxUI.showLoadingPanel();
    setTimeout(function() {
        $.post('index.php?module=Leads&action=getToken', {}).done(function (token) {
            SUGAR.ajaxUI.hideLoadingPanel();
            console.log("TOKEN", token);
            // Set up the Twilio Client device with the token
            device = new Twilio.Device(token, {
                logLevel: 1,
                // Set Opus as our preferred codec. Opus generally performs better, requiring less bandwidth and
                // providing better audio quality in restrained network conditions.
                codecPreferences: ["opus", "pcmu"]
            });
            device.updateToken(token);
            console.log("device", device);
            $('#callModal').modal('show');
            callControlsDiv = document.getElementById("call-controls");
            audioSelectionDiv = document.getElementById("output-selection");
            addDeviceListeners(device);
            $('#callModal').on('shown.bs.modal', function () {
              console.log("Initializing ...");
              callButton = document.getElementById("button-call");
              volumeIndicators = document.getElementById("volume-indicators");
              outgoingCallHangupButton = document.getElementById("button-hangup-outgoing");
              outputVolumeBar = document.getElementById("output-volume");
              inputVolumeBar = document.getElementById("input-volume");
              phoneNumberInput = document.getElementById("phone-number");
              speakerDevices = document.getElementById("speaker-devices");
              ringtoneDevices = document.getElementById("ringtone-devices");
              getAudioDevicesButton = document.getElementById("get-devices");
              incomingCallDiv = document.getElementById("incoming-call");
              incomingCallHangupButton = document.getElementById("button-hangup-incoming");
              incomingCallAcceptButton = document.getElementById("button-accept-incoming");
              incomingCallRejectButton = document.getElementById("button-reject-incoming");
              incomingPhoneNumberEl = document.getElementById("incoming-number");
              recordWrapper = document.querySelector('.record-wrapper');
              record = document.querySelector('.record');
              stop = document.querySelector('.stop');
              soundClips = document.querySelector('.sound-clips');
              canvas = document.querySelector('.visualizer');
              mainSection = document.querySelector('.main-controls');
            });
            // Device must be registered in order to receive incoming calls
            device.register();
        }).fail(function () {
            SUGAR.ajaxUI.hideLoadingPanel();
            console.log("ERROR");
        });
    }, 100);
}

function addDeviceListeners(device) {
    device.on("registered", function () {
        console.log("Twilio.Device Ready to make and receive calls!");
        callControlsDiv.classList.remove("hide");
    });

    device.on("error", function (error) {
        console.log("Twilio.Device Error: " + error.message);
    });

    device.on("incoming", handleIncomingCall);

    device.audio.on("deviceChange", updateAllAudioDevices.bind(device));

    // Show audio selection UI if it is supported by the browser.
    if (device.audio.isOutputSelectionSupported) {
        audioSelectionDiv.classList.remove("hide");
    }
}

$(document).on("click", "#button-call", function() {
    makeOutgoingCall();
});

$(document).on("click", "#get-devices", function() {
    getAudioDevices();
});

$(document).on("change", "#speaker-devices", function() {
    updateOutputDevice();
});

$(document).on("change", "#ringtone-devices", function() {
    updateRingtoneDevice();
});

// HANDLE INCOMING CALL
function handleIncomingCall(call) {
  console.log("Incoming call from", call.parameters.From);

  //show incoming call div and incoming phone number
  incomingCallDiv.classList.remove("hide");
  incomingPhoneNumberEl.innerHTML = call.parameters.From;

  //add event listeners for Accept, Reject, and Hangup buttons
  incomingCallAcceptButton.onclick = () => {
    acceptIncomingCall(call);
  };

  incomingCallRejectButton.onclick = () => {
    rejectIncomingCall(call);
  };

  incomingCallHangupButton.onclick = () => {
    hangupIncomingCall(call);
  };

  // add event listener to call object
  call.on("cancel", handleDisconnectedIncomingCall);
  call.on("disconnect", handleDisconnectedIncomingCall);
  call.on("reject", handleDisconnectedIncomingCall);
}

// ACCEPT INCOMING CALL

function acceptIncomingCall(call) {
  call.accept();

  //update UI
  log("Accepted incoming call.");
  incomingCallAcceptButton.classList.add("hide");
  incomingCallRejectButton.classList.add("hide");
  incomingCallHangupButton.classList.remove("hide");
}

// REJECT INCOMING CALL

function rejectIncomingCall(call) {
  call.reject();
  log("Rejected incoming call");
  resetIncomingCallUI();
}

// HANG UP INCOMING CALL

function hangupIncomingCall(call) {
  call.disconnect();
  log("Hanging up incoming call");
  resetIncomingCallUI();
}

// HANDLE CANCELLED INCOMING CALL

function handleDisconnectedIncomingCall() {
  log("Incoming call ended.");
  resetIncomingCallUI();
}

function resetIncomingCallUI() {
  incomingPhoneNumberEl.innerHTML = "";
  incomingCallAcceptButton.classList.remove("hide");
  incomingCallRejectButton.classList.remove("hide");
  incomingCallHangupButton.classList.add("hide");
  incomingCallDiv.classList.add("hide");
}

// MAKE AN OUTGOING CALL
async function makeOutgoingCall() {
    var params = {
      // get the phone number to call from the DOM
      To: phoneNumberInput.value,
      lead_id: $('input[name=record]').val()
    };

    if (device) {
      console.log("Attempting to call", params.To);

      // Twilio.Device.connect() returns a Call object
      const call = await device.connect({ params });

      // add listeners to the Call
      // "accepted" means the call has finished connecting and the state is now "open"
      call.on("accept", updateUIAcceptedOutgoingCall);
      call.on("disconnect", updateUIDisconnectedOutgoingCall);
      call.on("cancel", updateUIDisconnectedOutgoingCall);

      outgoingCallHangupButton.onclick = () => {
        console.log("Hanging up ...");
        call.disconnect();
      };

    } else {
        console.log("Unable to make call.");
    }
}

async function updateUIAcceptedOutgoingCall(call) {
    console.log("Call in progress ...");
    // call_stream = call.getLocalStream();
    callButton.disabled = true;
    outgoingCallHangupButton.classList.remove("hide");
    volumeIndicators.classList.remove("hide");
    bindVolumeIndicators(call);
    recordWrapper.classList.remove("hide");
    mainSection.classList.remove("hide");
    stop.disabled = true;
    window.onresize = function() {
      canvas.width = mainSection.offsetWidth;
    }
    window.onresize();

    // Record Call
    try {
      canvasCtx = canvas.getContext("2d");

      const stream = await navigator.mediaDevices.getUserMedia({
        audio: true,
        video: false
      });
      chunks = [];
      recorder = new MediaRecorder(stream, { type: mimeType });
      
      visualize(stream);
      
      recorder.addEventListener('dataavailable', event => {
        if (typeof event.data === 'undefined') return;
        if (event.data.size === 0) return;
        chunks.push(event.data);
      });

      recorder.addEventListener('stop', () => {
        console.log("data available after MediaRecorder.stop() called.");

        const blob = new Blob(chunks, {type: mimeType});
        const clipContainer = document.createElement('article');
        const audio = document.createElement('audio');

        clipContainer.classList.add('clip');
        audio.setAttribute('controls', '');

        clipContainer.appendChild(audio);
        soundClips.appendChild(clipContainer);
        audio.controls = true;
        const audioURL = URL.createObjectURL(blob);
        audio.src = audioURL;
        chunks = [];
        console.log("recorder stopped");
      });

      record.addEventListener('click', () => {
        recorder.start();
        console.log(recorder.state);
        console.log("recorder started");
        record.style.background = "red";
        record.style.color = "white";
        stop.disabled = false;
        record.disabled = true;
      });

      stop.addEventListener('click', () => {
        recorder.stop();
        console.log(recorder.state);
        console.log("recorder stopped");
        record.style.background = "";
        record.style.color = "";
        stop.disabled = true;
        record.disabled = false;
      });
    } catch {
      console.log("You denied access to the microphone so recording will not work.");
    }
}

function updateUIDisconnectedOutgoingCall() {
    console.log("Call disconnected.");
    callButton.disabled = false;
    outgoingCallHangupButton.classList.add("hide");
    volumeIndicators.classList.add("hide");
    mainSection.classList.add("hide");
    if(recorder && recorder.state == "recording") {
      recorder.stop();
    }
    // call_stream.getTracks().forEach( track => track.stop() );
    recorder = null;
    // call_stream = null;
}

function visualize(stream) {
  if(!audioCtx) {
    audioCtx = new AudioContext();
  }

  const source = audioCtx.createMediaStreamSource(stream);

  const analyser = audioCtx.createAnalyser();
  analyser.fftSize = 2048;
  const bufferLength = analyser.frequencyBinCount;
  const dataArray = new Uint8Array(bufferLength);

  source.connect(analyser);
  //analyser.connect(audioCtx.destination);

  draw()

  function draw() {
    const WIDTH = canvas.width
    const HEIGHT = canvas.height;

    requestAnimationFrame(draw);

    analyser.getByteTimeDomainData(dataArray);

    canvasCtx.fillStyle = 'rgb(200, 200, 200)';
    canvasCtx.fillRect(0, 0, WIDTH, HEIGHT);

    canvasCtx.lineWidth = 2;
    canvasCtx.strokeStyle = 'rgb(0, 0, 0)';

    canvasCtx.beginPath();

    let sliceWidth = WIDTH * 1.0 / bufferLength;
    let x = 0;


    for(let i = 0; i < bufferLength; i++) {

      let v = dataArray[i] / 128.0;
      let y = v * HEIGHT/2;

      if(i === 0) {
        canvasCtx.moveTo(x, y);
      } else {
        canvasCtx.lineTo(x, y);
      }

      x += sliceWidth;
    }

    canvasCtx.lineTo(canvas.width, canvas.height/2);
    canvasCtx.stroke();

  }
}

// AUDIO CONTROLS

async function getAudioDevices() {
    await navigator.mediaDevices.getUserMedia({ audio: true, video: false });
    updateAllAudioDevices.bind(device);
}

function updateAllAudioDevices() {
    if (device) {
      updateDevices(speakerDevices, device.audio.speakerDevices.get());
      updateDevices(ringtoneDevices, device.audio.ringtoneDevices.get());
    }
}

function updateOutputDevice() {
    const selectedDevices = Array.from(speakerDevices.children)
      .filter((node) => node.selected)
      .map((node) => node.getAttribute("data-id"));

    device.audio.speakerDevices.set(selectedDevices);
}

function updateRingtoneDevice() {
    const selectedDevices = Array.from(ringtoneDevices.children)
      .filter((node) => node.selected)
      .map((node) => node.getAttribute("data-id"));

    device.audio.ringtoneDevices.set(selectedDevices);
}

function bindVolumeIndicators(call) {
    call.on("volume", function (inputVolume, outputVolume) {
      var inputColor = "red";
      if (inputVolume < 0.5) {
        inputColor = "green";
      } else if (inputVolume < 0.75) {
        inputColor = "yellow";
      }

      inputVolumeBar.style.width = Math.floor(inputVolume * 300) + "px";
      inputVolumeBar.style.background = inputColor;

      var outputColor = "red";
      if (outputVolume < 0.5) {
        outputColor = "green";
      } else if (outputVolume < 0.75) {
        outputColor = "yellow";
      }

      outputVolumeBar.style.width = Math.floor(outputVolume * 300) + "px";
      outputVolumeBar.style.background = outputColor;
    });
}

// Update the available ringtone and speaker devices
function updateDevices(selectEl, selectedDevices) {
    selectEl.innerHTML = "";

    device.audio.availableOutputDevices.forEach(function (device, id) {
      var isActive = selectedDevices.size === 0 && id === "default";
      selectedDevices.forEach(function (device) {
        if (device.deviceId === id) {
          isActive = true;
        }
      });

      var option = document.createElement("option");
      option.label = device.label;
      option.setAttribute("data-id", id);
      if (isActive) {
        option.setAttribute("selected", "selected");
      }
      selectEl.appendChild(option);
    });
}