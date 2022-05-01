<div class="modal fade" id="callModal" tabindex="-1" role="dialog" aria-labelledby="callModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header call-header">
        <button type="button" class="close cross-icon" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title call-modal-title" id="callModalLabel">Make a Call</h4>
      </div>
      <div class="modal-body">
        <main id="controls" class="call-main">
            <section class="left-column" id="info">
                <div id="output-selection" class="hide">
                <label class="call-label">Ringtone Devices</label>
                <select id="ringtone-devices" class="sound-devices" multiple></select>
                <label class="call-label">Speaker Devices</label>
                <select id="speaker-devices" class="sound-devices" multiple></select
                ><br />
                <button id="get-devices" class="button primary call-button">Unknown devices?</button>
                </div>
            </section>
            <section class="center-column">
                <div id="call-controls" class="hide">
                    <input id="phone-number" class="phone-number" type="text" value={$phone_mobile} readonly />
                    <button id="button-call" class="button primary call-button">Call</button>
                    <button id="button-hangup-outgoing" class="button danger hide call-button">Hang Up</button>
                    <div id="incoming-call" class="hide">
                      <h2>Incoming Call Controls</h2>
                      <p class="instructions">
                        Incoming Call from <span id="incoming-number"></span>
                      </p>
                      <button id="button-accept-incoming">Accept</button>
                      <button id="button-reject-incoming">Reject</button>
                      <button id="button-hangup-incoming" class="hide">Hangup</button>
                    </div>
                    <div id="volume-indicators" class="hide">
                        <label class="call-label">Mic Volume</label>
                        <div id="input-volume"></div>
                        <br /><br />
                        <label class="call-label">Speaker Volume</label>
                        <div id="output-volume"></div>
                    </div>
                </div>
            </section>
        </main>
        <div class="record-wrapper hide">
          <section class="main-controls hide">
            <canvas class="visualizer" height="60px"></canvas>
            <div id="buttons">
              <button class="button primary record">Record</button>
              <button class="button primary stop">Stop</button>
            </div>
          </section>
          <section class="sound-clips"></section>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>