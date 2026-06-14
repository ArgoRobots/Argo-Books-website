/* Landing-page upload handler. Takes the chosen/dropped spreadsheet, POSTs it to
   upload.php, shows a loading overlay during analysis, then hands the result to
   the result page via sessionStorage (Option A: nothing is stored server-side). */
(function(){
  var TOOL = window.PA_TOOL || '';
  var RESULTS = window.PA_RESULTS || (TOOL + 'results/');
  var MAX = 5 * 1024 * 1024;
  var ALLOWED = ['xlsx','csv'];

  var input = document.getElementById('paFile');
  var drop = document.getElementById('paDrop');
  var overlay = document.getElementById('paOverlay');
  var overlaySub = document.getElementById('paOverlaySub');
  var errBox = document.getElementById('paError');
  var cancelBtn = document.getElementById('paCancel');
  if(!input) return;

  var controller = null;   // AbortController for the in-flight upload
  var cancelled = false;

  function showError(msg, cta){
    if(!errBox) return;
    errBox.innerHTML = msg + (cta ? ' <a href="'+cta+'">Try Argo Books free →</a>' : '');
    errBox.hidden = false;
  }
  function clearError(){ if(errBox){ errBox.hidden = true; errBox.textContent=''; } }

  var subTimer = null;
  function showOverlay(){
    if(overlay) overlay.hidden = false;
    var msgs = ['Detecting columns and cleaning your data…','Mapping your sheets to a clean structure…','Crunching the numbers and building your charts…'];
    var i = 0;
    if(overlaySub) subTimer = setInterval(function(){ i=(i+1)%msgs.length; overlaySub.textContent = msgs[i]; }, 6000);
  }
  function hideOverlay(){ if(overlay) overlay.hidden = true; if(subTimer){ clearInterval(subTimer); subTimer=null; } }

  function handle(file){
    if(!file) return;
    clearError();
    var ext = (file.name.split('.').pop() || '').toLowerCase();
    if(ALLOWED.indexOf(ext) === -1){ showError('Please upload an .xlsx or .csv spreadsheet.'); return; }
    if(file.size > MAX){ showError('That file is over 5 MB. Trim it down or split it and try again.'); return; }

    cancelled = false;
    controller = ('AbortController' in window) ? new AbortController() : null;
    showOverlay();
    var fd = new FormData();
    fd.append('file', file);

    fetch(TOOL + 'upload.php', { method:'POST', body:fd, signal: controller ? controller.signal : undefined })
      .then(function(r){ return r.json().then(function(j){ return { status:r.status, body:j }; }); })
      .then(function(res){
        if(cancelled) return;
        var j = res.body || {};
        if(res.status === 200 && j.ok){
          try {
            sessionStorage.setItem('pa_result', JSON.stringify({ analytics:j.analytics, normalized:j.normalized }));
          } catch(e){
            // Too large for sessionStorage: keep just the analytics so charts still render.
            try { sessionStorage.setItem('pa_result', JSON.stringify({ analytics:j.analytics, normalized:null })); } catch(e2){}
          }
          window.location.href = RESULTS;
          return;
        }
        hideOverlay();
        showError(j.message || 'We could not analyze that spreadsheet. Please try a cleaner export.', j.cta);
      })
      .catch(function(){
        // User cancelled: ignore. The server keeps running (ignore_user_abort) and
        // still records the daily usage, so Cancel can't dodge the rate limit.
        if(cancelled) return;
        hideOverlay();
        showError('Something went wrong uploading your file. Please try again.');
      });
  }

  // Cancel: stop waiting and return to the upload state. The request is aborted
  // client-side, but the server finishes the analysis and records usage anyway.
  if(cancelBtn){ cancelBtn.addEventListener('click', function(){
    cancelled = true;
    if(controller){ try { controller.abort(); } catch(e){} }
    hideOverlay();
    if(input) input.value = ''; // let the same file be re-selected
  }); }

  input.addEventListener('change', function(){ if(input.files && input.files[0]) handle(input.files[0]); });

  // Drag-and-drop onto the upload box.
  if(drop){
    ['dragenter','dragover'].forEach(function(ev){ drop.addEventListener(ev, function(e){ e.preventDefault(); drop.classList.add('dragover'); }); });
    ['dragleave','drop'].forEach(function(ev){ drop.addEventListener(ev, function(e){ e.preventDefault(); drop.classList.remove('dragover'); }); });
    drop.addEventListener('drop', function(e){ var dt=e.dataTransfer; if(dt && dt.files && dt.files[0]) handle(dt.files[0]); });
  }
})();
