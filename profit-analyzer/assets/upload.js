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
  if(!input) return;

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

    showOverlay();
    var fd = new FormData();
    fd.append('file', file);

    fetch(TOOL + 'upload.php', { method:'POST', body:fd })
      .then(function(r){ return r.json().then(function(j){ return { status:r.status, body:j }; }); })
      .then(function(res){
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
        hideOverlay();
        showError('Something went wrong uploading your file. Please try again.');
      });
  }

  input.addEventListener('change', function(){ if(input.files && input.files[0]) handle(input.files[0]); });

  // Drag-and-drop onto the upload box.
  if(drop){
    ['dragenter','dragover'].forEach(function(ev){ drop.addEventListener(ev, function(e){ e.preventDefault(); drop.classList.add('dragover'); }); });
    ['dragleave','drop'].forEach(function(ev){ drop.addEventListener(ev, function(e){ e.preventDefault(); drop.classList.remove('dragover'); }); });
    drop.addEventListener('drop', function(e){ var dt=e.dataTransfer; if(dt && dt.files && dt.files[0]) handle(dt.files[0]); });
  }
})();
