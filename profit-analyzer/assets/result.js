/* Profit Analyzer result page — renders the live analytics payload.
   Data comes from window.PA_ANALYTICS (an upload, handed over via sessionStorage)
   or, when absent, the bundled sample. Charts/cards/tabs with no data are hidden,
   so a real upload shows only the dimensions its spreadsheet supports.
   ECharts is loaded separately. window.PA_ASSETS = base path for self-hosted assets. */
(function(){
  var ASSETS = window.PA_ASSETS || '';
  var TOOL = window.PA_TOOL || '';
  var PALETTE = ['#3b82f6','#10b981','#f59e0b','#ef4444','#8b5cf6','#06b6d4','#ec4899','#64748b','#14b8a6','#f97316'];
  var charts = {};
  var dark = false, curTab = 'dashboard';
  var A = null; // the analytics payload

  function TT(){ return dark
    ? {axis:'#8a97ab',split:'rgba(255,255,255,.08)',axisLine:'rgba(255,255,255,.13)',tipBg:'#111c33',tipBorder:'rgba(255,255,255,.12)',tipText:'#e2e8f0',sankN:'#cbd5e1',sankV:'#8a97ab'}
    : {axis:'#94a3b8',split:'#f1f5f9',axisLine:'#e2e8f0',tipBg:'#ffffff',tipBorder:'#e6ebf2',tipText:'#0f172a',sankN:'#475569',sankV:'#9aa6b6'}; }
  function tip(extra){ var t=TT(); return Object.assign({backgroundColor:t.tipBg,borderColor:t.tipBorder,borderWidth:1,textStyle:{color:t.tipText,fontFamily:'Hanken Grotesk',fontSize:12.5}}, extra||{}); }
  function init(id, opt){ var d=document.getElementById(id); if(!d||charts[id])return; var c=echarts.init(d,null,{renderer:'svg'}); c.setOption(opt); charts[id]=c; }
  function base(o){ o.backgroundColor='transparent'; o.textStyle={fontFamily:'Hanken Grotesk'}; return o; }
  function axisX(cats){ var t=TT(); return {type:'category',data:cats,boundaryGap:true,axisLine:{lineStyle:{color:t.axisLine}},axisTick:{show:false},axisLabel:{color:t.axis,fontSize:11}}; }
  function axisY(){ var t=TT(); return {type:'value',splitLine:{lineStyle:{color:t.split}},axisLine:{show:false},axisLabel:{color:t.axis,fontSize:11}}; }
  function fade(hex){ return new echarts.graphic.LinearGradient(0,0,0,1,[{offset:0,color:hex+'33'},{offset:1,color:hex+'00'}]); }
  function esc(s){ var d=document.createElement('div'); d.textContent=(s==null?'':String(s)); return d.innerHTML; }

  function line(cats, series){
    return base({ tooltip:tip({trigger:'axis'}), legend: series.length>1?{top:0,icon:'roundRect',itemWidth:11,itemHeight:11,textStyle:{color:TT().axis,fontSize:12}}:undefined,
      grid:{left:52,right:18,top:series.length>1?34:14,bottom:28}, xAxis:axisX(cats), yAxis:axisY(),
      series:series.map(function(s){ return {name:s.name,type:'line',smooth:true,showSymbol:false,data:s.data,lineStyle:{width:3,color:s.color},itemStyle:{color:s.color},areaStyle:s.area?{color:fade(s.color)}:null}; }) });
  }
  function bars(cats, series, opt){
    opt=opt||{};
    return base({ tooltip:tip({trigger:'axis',axisPointer:{type:'shadow'}}), legend: series.length>1?{top:0,icon:'roundRect',itemWidth:11,itemHeight:11,textStyle:{color:TT().axis,fontSize:12}}:undefined,
      grid:{left:52,right:18,top:series.length>1?34:14,bottom:28}, xAxis:axisX(cats), yAxis:axisY(),
      series:series.map(function(s){ return {name:s.name,type:'bar',stack:opt.stack?'t':null,data:s.data,barMaxWidth:opt.thin?16:26,itemStyle:{color:s.color,borderRadius:opt.stack?0:[4,4,0,0]}}; }) });
  }
  function pie(data, money){
    return base({ tooltip:tip({trigger:'item',formatter: money?'{b}: ${c} ({d}%)':'{b}: {c} ({d}%)'}),
      legend:{type:'scroll',orient:'vertical',right:6,top:'middle',icon:'circle',itemWidth:10,itemHeight:10,textStyle:{color:TT().axis,fontSize:12}},
      series:[{type:'pie',radius:['46%','72%'],center:['36%','52%'],avoidLabelOverlap:true,label:{show:false},
        itemStyle:{borderColor:'#fff',borderWidth:2,borderRadius:4},
        data:data.map(function(d,i){ return {name:d.name,value:d.value,itemStyle:{color:PALETTE[i%PALETTE.length]}}; })}] });
  }

  // hide the card wrapping a chart id (when there's no data for it)
  function hideCard(id){ var d=document.getElementById(id); if(d){ var c=d.closest('.chartcard,.listcard'); if(c)c.style.display='none'; } }
  function chart(id, opt){ if(opt){ init(id,opt); } else { hideCard(id); } }
  // pie helper that hides the card when the dataset is empty
  function pieOr(id, data, money){ chart(id, (data&&data.length)? pie(data,money):null); }
  function has(arr){ return arr && arr.length; }

  // ---------- Sankey (dashboard money-flow) ----------
  function sankeyColor(name){
    var n=name.toLowerCase();
    if(n==='revenue') return '#6f8fb3';
    if(n==='profit') return '#1f9d6b';
    if(/cost|ads|advert|fee|other|shipping|expense/.test(n)) return '#d76b66'; // leaks (red)
    return '#6fae93'; // surviving stages (green)
  }
  function sankey(flow){
    var el=document.getElementById('sankeyChart'); if(!el||charts.sankey||!flow)return;
    var c=echarts.init(el,null,{renderer:'svg'}); charts.sankey=c;
    var VAL=flow.nodes||{}; var REV=VAL['Revenue']||1;
    function f(n){return '$'+Number(n).toLocaleString('en-US');}
    var L=(flow.links||[]).map(function(a){return {source:a[0],target:a[1],value:a[2]};});
    var t=TT(), pn=dark?'#5cf0b4':'#0f766e', pv=dark?'#34d399':'#10a37f';
    var nodes=Object.keys(VAL).map(function(n){ var o={name:n,value:VAL[n],itemStyle:{color:sankeyColor(n),borderWidth:0,borderRadius:6}};
      if(n==='Revenue')o.label={position:'left'};
      if(n==='Profit')o.label={position:'right',rich:{n:{color:pn,fontSize:12.5,fontWeight:700,lineHeight:17},v:{color:pv,fontSize:12,fontWeight:700,lineHeight:15}}};
      return o; });
    c.setOption({backgroundColor:'transparent',animationDuration:1100,
      tooltip:tip({trigger:'item',confine:true,padding:[9,13],extraCssText:'box-shadow:0 12px 30px -10px rgba(16,24,40,.22);border-radius:10px',
        formatter:function(p){ if(p.dataType==='node')return '<b>'+p.name+'</b><br><span style="opacity:.7">'+f(p.value)+' · '+Math.round(p.value/REV*100)+'%</span>'; return '<span style="opacity:.7">'+p.data.source+' → '+p.data.target+'</span><br><b>'+f(p.value)+'</b>'; }}),
      series:[{type:'sankey',left:92,right:116,top:12,bottom:24,nodeWidth:12,nodeGap:18,nodeAlign:'left',draggable:false,
        emphasis:{focus:'adjacency',lineStyle:{opacity:.5}},lineStyle:{color:'gradient',curveness:.52,opacity:.3},
        label:{fontFamily:'Hanken Grotesk',position:'right',formatter:function(p){return '{n|'+p.name+'}\n{v|'+f(p.value)+'}';},rich:{n:{fontSize:12.5,fontWeight:600,color:t.sankN,lineHeight:17},v:{fontSize:11.5,fontWeight:500,color:t.sankV,lineHeight:15}}},
        data:nodes,links:L}]});
  }

  // ---------- per-tab chart builders (read from A) ----------
  var builders = {
    dashboard:function(){
      var d=A.dashboard||{};
      sankey(d.flow);
      chart('c_profitTrend', d.profitTrend? line(d.profitTrend.cats,[{name:'Profit',data:d.profitTrend.data,color:'#10b981',area:true}]):null);
      var sve=d.salesVsExp;
      chart('c_salesVsExp', sve? bars(sve.revenue.cats,[{name:'Sales',data:sve.revenue.data,color:'#3b82f6'},{name:'Expenses',data:sve.expenses.data,color:'#ef4444'}]):null);
      chart('c_salesTrend', (sve&&sve.revenue)? line(sve.revenue.cats,[{name:'Revenue',data:sve.revenue.data,color:'#3b82f6',area:true}]):null);
      pieOr('c_revDist', d.revByProduct, true);
      chart('c_purchTrend', (sve&&sve.expenses)? line(sve.expenses.cats,[{name:'Expenses',data:sve.expenses.data,color:'#ef4444',area:true}]):null);
      pieOr('c_expDist', d.expDist, true);
    },
    products:function(){ hideCard('c_prodTrend'); }, // table is rendered separately; no trend series in the payload
    geographic:function(){
      var g=A.geographic||{};
      pieOr('c_cOrigin', g.origin);
      hideCard('c_compOrigin');
      pieOr('c_cDest', g.destination);
      hideCard('c_compDest');
      if(has(g.map)) geoMap(g.map); else hideCard('c_geoMap');
    },
    customers:function(){
      var c=A.customers||{};
      pieOr('c_topCust', c.topCustomers, true);
      pieOr('c_payStatus', c.paymentStatus);
      ['c_custGrowth','c_clv','c_activeInactive','c_rentalsPer'].forEach(hideCard);
    },
    taxes:function(){
      var t=A.taxes||{};
      var cvp=t.collectedVsPaid;
      chart('c_taxVsPaid', cvp? bars(cvp.collected.cats,[{name:'Collected',data:cvp.collected.data,color:'#10b981'},{name:'Paid',data:cvp.paid.data,color:'#ef4444'}]):null);
      pieOr('c_taxCat', t.byCategory);
      ['c_taxRate','c_taxLiab','c_taxProd','c_expRevTax'].forEach(hideCard);
    }
  };

  // ---------- world map ----------
  var mapReady=false, mapPending=false;
  function geoMap(mapData){
    var el=document.getElementById('c_geoMap'); if(!el||charts.geoMap)return;
    var maxv=mapData.reduce(function(m,d){return Math.max(m,d.value);},0)||1;
    function draw(){
      var c=echarts.init(el,null,{renderer:'svg'}); charts.geoMap=c; var t=TT();
      c.setOption(base({ tooltip:tip({trigger:'item',formatter:function(p){return p.name+(p.value?': $'+Number(p.value).toLocaleString():': —');}}),
        visualMap:{left:14,bottom:14,min:0,max:maxv,calculable:true,inRange:{color:dark?['#1e3356','#2f6fd0','#7db4ff']:['#dbeafe','#60a5fa','#1e40af']},text:['High','Low'],textStyle:{color:t.axis}},
        series:[{type:'map',map:'world',roam:false,itemStyle:{areaColor:dark?'#16223c':'#eef2f7',borderColor:dark?'rgba(255,255,255,.10)':'#dbe2ec'},emphasis:{itemStyle:{areaColor:'#10b981'},label:{show:false}},
          data:mapData}] }));
    }
    if(mapReady){ draw(); return; }
    if(mapPending)return; mapPending=true;
    fetch(ASSETS + 'world.json').then(function(r){return r.json();})
      .then(function(geo){ echarts.registerMap('world',geo); mapReady=true; draw(); })
      .catch(function(){ el.innerHTML='<div style="padding:30px;text-align:center;color:#94a3b8;font-size:13px">World map could not load.</div>'; });
  }

  // ---------- KPI + table + meta rendering ----------
  function renderKpis(panelName, kpis){
    var panel=document.querySelector('.panel[data-panel="'+panelName+'"]'); if(!panel)return;
    var box=panel.querySelector('.kpis'); if(!box)return;
    if(!has(kpis)){ box.style.display='none'; return; }
    box.innerHTML=kpis.map(function(k){
      return '<div class="kpi '+(k.cls||'')+'"><div class="lbl">'+esc(k.lbl)+'</div><div class="val">'+esc(k.val)+'</div>'
        + (k.sub?'<div class="sub '+(k.subcls||'')+'">'+esc(k.sub)+'</div>':'') + '</div>';
    }).join('');
  }
  function renderProductsTable(){
    var p=A.products; var tbody=document.querySelector('.panel[data-panel="products"] .dtable tbody'); if(!tbody)return;
    if(!p||!has(p.table)){ return; }
    tbody.innerHTML=p.table.map(function(r){
      return '<tr><td>'+esc(r.name)+'</td><td class="num">'+esc(r.units)+'</td><td class="num">'+esc(r.rev)+'</td><td class="num">'+esc(r.avg)+'</td></tr>';
    }).join('');
  }
  function renderFlowStat(){
    var d=A.dashboard; if(!d||!d.flow)return;
    var stat=document.querySelector('.keptstat b'); if(stat)stat.textContent=(d.flow.kept||0)+'%';
  }
  function renderMeta(){
    var m=A.meta||{};
    var fileEl=document.querySelector('.rhead .file'); if(fileEl){
      var badge=fileEl.querySelector('.badge'); var name=esc(m.filename||'your-spreadsheet.xlsx');
      fileEl.childNodes.forEach&&fileEl.childNodes.forEach(function(n){ if(n.nodeType===3)n.textContent=' '+m.filename+' '; });
      if(badge)badge.textContent='✓ '+Number(m.rows||0).toLocaleString()+' rows analyzed';
    }
  }
  function renderCleaned(){
    var tbody=document.querySelector('.cleantable tbody'); if(!tbody||!A.cleaned)return;
    var fmt=function(n){ return '$'+Number(n).toLocaleString('en-US',{minimumFractionDigits:2,maximumFractionDigits:2}); };
    tbody.innerHTML=A.cleaned.map(function(r){
      var inc=r.type==='income';
      return '<tr data-type="'+esc(r.type)+'"><td class="date">'+esc(r.date)+'</td><td>'+esc(r.description)+'</td>'
        +'<td><span class="pill cat">'+esc(r.category)+'</span></td>'
        +'<td><span class="pill '+(inc?'inc':'exp')+'">'+(inc?'Income':'Expense')+'</span></td>'
        +'<td class="amt'+(inc?' inc':'')+'">'+fmt(r.amount)+'</td></tr>';
    }).join('');
    wireCleanedFilter();
  }

  // ---------- tabs ----------
  function show(name){
    curTab=name;
    document.querySelectorAll('.tabbtn').forEach(function(b){ b.classList.toggle('active', b.dataset.tab===name); });
    document.querySelectorAll('.panel').forEach(function(p){ p.classList.toggle('active', p.dataset.panel===name); });
    if(builders[name] && !built[name]){ built[name]=true; builders[name](); }
    Object.keys(charts).forEach(function(k){ charts[k].resize(); });
  }
  var built={};

  function hideAbsentTabs(){
    var present={}; (A.tabs||[]).forEach(function(t){ present[t]=true; });
    document.querySelectorAll('.tabbtn').forEach(function(b){ if(!present[b.dataset.tab]) b.style.display='none'; });
    document.querySelectorAll('.panel').forEach(function(p){ if(!present[p.dataset.panel]) p.style.display='none'; });
  }

  // ---------- cleaned-table filter ----------
  function wireCleanedFilter(){
    var ctabs=document.querySelectorAll('.cleanbar .ctab');
    var rows=Array.prototype.slice.call(document.querySelectorAll('.cleantable tbody tr'));
    var label=document.getElementById('rowcount');
    var total=rows.length;
    function apply(f){ var n=0; rows.forEach(function(r){ var m=(f==='all')||(r.dataset.type===f); r.style.display=m?'':'none'; if(m)n++; });
      if(label) label.textContent=(f==='all')?(n+' of '+total+' rows shown'):(n+' '+f+' rows shown'); }
    ctabs.forEach(function(t){ t.onclick=function(){ ctabs.forEach(function(x){x.classList.remove('active');}); t.classList.add('active'); apply(t.dataset.filter); }; });
    apply('all');
  }

  // ---------- download + email (post the client-held normalized data) ----------
  function wireActions(){
    var norm=window.PA_NORMALIZED || null;
    function postBlob(url){
      return fetch(url,{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({normalized:norm})});
    }
    document.querySelectorAll('.btn-download, .cleanbar .dl').forEach(function(btn){
      btn.addEventListener('click', function(){
        if(!norm){ return; }
        btn.classList.add('busy');
        postBlob(TOOL+'download.php').then(function(r){ return r.blob(); }).then(function(blob){
          var url=URL.createObjectURL(blob); var a=document.createElement('a');
          a.href=url; a.download='cleaned-'+(new Date().toISOString().slice(0,10))+'.xlsx';
          document.body.appendChild(a); a.click(); a.remove(); URL.revokeObjectURL(url);
        }).catch(function(){}).then(function(){ btn.classList.remove('busy'); });
      });
    });
    var form=document.querySelector('.email form');
    if(form){ form.addEventListener('submit', function(e){ e.preventDefault();
      var input=form.querySelector('input[type=email]'); var btn=form.querySelector('button');
      var email=(input.value||'').trim(); if(!email)return;
      btn.disabled=true; var old=btn.textContent; btn.textContent='Sending…';
      fetch(TOOL+'email.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({email:email,normalized:norm})})
        .then(function(r){return r.json();}).then(function(j){ btn.textContent=j&&j.ok?'Sent ✓':(j&&j.message?'Try again':'Failed'); })
        .catch(function(){ btn.textContent='Failed'; })
        .then(function(){ setTimeout(function(){ btn.textContent=old; btn.disabled=false; },2500); });
    }); }
  }

  // ---------- dark toggle ----------
  function wireTheme(){
    var tbtn=document.getElementById('themeToggle'); if(!tbtn)return;
    tbtn.addEventListener('click', function(){
      dark=!dark;
      document.documentElement.setAttribute('data-theme', dark?'dark':'light');
      tbtn.setAttribute('aria-pressed', dark);
      Object.keys(charts).forEach(function(k){ charts[k].dispose(); delete charts[k]; });
      Object.keys(built).forEach(function(k){ delete built[k]; });
      mapReady=mapReady; // keep registered map
      built[curTab]=true; builders[curTab] && builders[curTab]();
      Object.keys(charts).forEach(function(k){ charts[k].resize(); });
    });
  }

  // ---------- boot ----------
  function showEmptyState(){
    var tabbar=document.querySelector('.tabbar'); if(tabbar)tabbar.style.display='none';
    document.querySelectorAll('.panel').forEach(function(p){ p.style.display='none'; });
    var wrap=document.querySelector('.wrap');
    if(wrap){ wrap.insertAdjacentHTML('beforeend',
      '<div class="empty-note" style="text-align:center;max-width:560px;margin:30px auto;padding:26px;border:1px solid var(--line,#e6ebf2);border-radius:16px;background:#fff">'
      +'<h3 style="margin:0 0 8px;font-family:Fraunces,Georgia,serif">We cleaned your file</h3>'
      +'<p style="color:#64748b;margin:0">It didn\'t contain enough sales or expense transactions to chart, but your cleaned, organized spreadsheet is ready below.</p></div>'); }
  }

  function render(){
    renderMeta();
    hideAbsentTabs();
    if(!has(A.tabs)){ showEmptyState(); renderCleaned(); wireActions(); return; }
    ['dashboard','products','customers','taxes'].forEach(function(name){ if(A[name]) renderKpis(name, A[name].kpis); });
    renderFlowStat();
    renderProductsTable();
    renderCleaned();
    document.getElementById('tabbar').addEventListener('click', function(e){ var b=e.target.closest('.tabbtn'); if(b && b.style.display!=='none') show(b.dataset.tab); });
    var first=(A.tabs&&A.tabs[0])||'dashboard';
    show(first);
    wireTheme();
    wireActions();
    window.addEventListener('resize', function(){ Object.keys(charts).forEach(function(k){ charts[k].resize(); }); });
  }

  function boot(data){ A=data||{}; if(!A.tabs)A.tabs=[]; render(); }

  if(window.PA_ANALYTICS){ boot(window.PA_ANALYTICS); }
  else {
    fetch(ASSETS+'sample-analytics.json').then(function(r){return r.json();})
      .then(function(d){ if(!window.PA_NORMALIZED){ // also load sample normalized so download/email work in the demo
          return fetch(ASSETS+'sample-normalized.json').then(function(r){return r.json();}).then(function(n){ window.PA_NORMALIZED=n; boot(d); }).catch(function(){ boot(d); });
        } boot(d); })
      .catch(function(){ document.querySelector('.wrap').insertAdjacentHTML('afterbegin','<p style="padding:20px;color:#ef4444">Could not load results. Please try uploading again.</p>'); });
  }
})();
