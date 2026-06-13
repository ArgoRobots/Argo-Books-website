/* Profit Analyzer result page — full analytics, lazy-loaded per tab, dark-mode
   toggle. Sample data for now; the real build feeds NormalizedData here.
   ECharts is loaded separately. window.PA_ASSETS = base path for self-hosted assets. */
(function(){
  var ASSETS = window.PA_ASSETS || '';
  var PALETTE = ['#3b82f6','#10b981','#f59e0b','#ef4444','#8b5cf6','#06b6d4','#ec4899','#64748b','#14b8a6','#f97316'];
  var M = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
  var charts = {};
  var dark = false, curTab = 'dashboard';
  function TT(){ return dark
    ? {axis:'#8a97ab',split:'rgba(255,255,255,.08)',axisLine:'rgba(255,255,255,.13)',tipBg:'#111c33',tipBorder:'rgba(255,255,255,.12)',tipText:'#e2e8f0',sankN:'#cbd5e1',sankV:'#8a97ab'}
    : {axis:'#94a3b8',split:'#f1f5f9',axisLine:'#e2e8f0',tipBg:'#ffffff',tipBorder:'#e6ebf2',tipText:'#0f172a',sankN:'#475569',sankV:'#9aa6b6'}; }
  function tip(extra){ var t=TT(); return Object.assign({backgroundColor:t.tipBg,borderColor:t.tipBorder,borderWidth:1,textStyle:{color:t.tipText,fontFamily:'Hanken Grotesk',fontSize:12.5}}, extra||{}); }
  function init(id, opt){ var d=document.getElementById(id); if(!d||charts[id])return; var c=echarts.init(d,null,{renderer:'svg'}); c.setOption(opt); charts[id]=c; }
  function base(o){ o.backgroundColor='transparent'; o.textStyle={fontFamily:'Hanken Grotesk'}; return o; }
  function axisX(cats){ var t=TT(); return {type:'category',data:cats,boundaryGap:true,axisLine:{lineStyle:{color:t.axisLine}},axisTick:{show:false},axisLabel:{color:t.axis,fontSize:11}}; }
  function axisY(){ var t=TT(); return {type:'value',splitLine:{lineStyle:{color:t.split}},axisLine:{show:false},axisLabel:{color:t.axis,fontSize:11}}; }
  function fade(hex){ return new echarts.graphic.LinearGradient(0,0,0,1,[{offset:0,color:hex+'33'},{offset:1,color:hex+'00'}]); }

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

  // ---------- Sankey (dashboard) ----------
  function sankey(){
    var el=document.getElementById('sankeyChart'); if(!el||charts.sankey)return;
    var c=echarts.init(el,null,{renderer:'svg'}); charts.sankey=c;
    var REV=24800; function f(n){return '$'+n.toLocaleString('en-US');}
    var VAL={'Revenue':24800,'Cost of goods':13400,'Gross profit':11400,'Ads & marketing':4460,'After ads':6940,'Fees':2230,'Profit':4710};
    var L=[['Revenue','Cost of goods',13400],['Revenue','Gross profit',11400],['Gross profit','Ads & marketing',4460],['Gross profit','After ads',6940],['After ads','Fees',2230],['After ads','Profit',4710]].map(function(a){return {source:a[0],target:a[1],value:a[2]};});
    var C={'Revenue':'#6f8fb3','Cost of goods':'#d76b66','Gross profit':'#6fae93','Ads & marketing':'#e3938c','After ads':'#46a37e','Fees':'#c9544f','Profit':'#1f9d6b'};
    var t=TT(), pn=dark?'#5cf0b4':'#0f766e', pv=dark?'#34d399':'#10a37f';
    var nodes=Object.keys(VAL).map(function(n){ var o={name:n,value:VAL[n],itemStyle:{color:C[n],borderWidth:0,borderRadius:6}}; if(n==='Revenue')o.label={position:'left'}; if(n==='Profit')o.label={position:'right',rich:{n:{color:pn,fontSize:12.5,fontWeight:700,lineHeight:17},v:{color:pv,fontSize:12,fontWeight:700,lineHeight:15}}}; return o; });
    c.setOption({backgroundColor:'transparent',animationDuration:1100,
      tooltip:tip({trigger:'item',confine:true,padding:[9,13],extraCssText:'box-shadow:0 12px 30px -10px rgba(16,24,40,.22);border-radius:10px',
        formatter:function(p){ if(p.dataType==='node')return '<b>'+p.name+'</b><br><span style="opacity:.7">'+f(p.value)+' · '+Math.round(p.value/REV*100)+'%</span>'; return '<span style="opacity:.7">'+p.data.source+' → '+p.data.target+'</span><br><b>'+f(p.value)+'</b>'; }}),
      series:[{type:'sankey',left:92,right:116,top:12,bottom:24,nodeWidth:12,nodeGap:18,nodeAlign:'left',draggable:false,
        emphasis:{focus:'adjacency',lineStyle:{opacity:.5}},lineStyle:{color:'gradient',curveness:.52,opacity:.3},
        label:{fontFamily:'Hanken Grotesk',position:'right',formatter:function(p){return '{n|'+p.name+'}\n{v|'+f(p.value)+'}';},rich:{n:{fontSize:12.5,fontWeight:600,color:t.sankN,lineHeight:17},v:{fontSize:11.5,fontWeight:500,color:t.sankV,lineHeight:15}}},
        data:nodes,links:L}]});
  }

  // ---------- per-tab builders ----------
  var built={};
  var builders = {
    dashboard:function(){
      sankey();
      init('c_profitTrend', line(M.slice(0,6),[{name:'Profit',data:[420,560,510,680,720,860],color:'#10b981',area:true}]));
      init('c_salesVsExp', bars(M.slice(0,6),[{name:'Sales',data:[3.6,3.9,3.7,4.2,4.4,5.0].map(function(x){return x*1000;}),color:'#3b82f6'},{name:'Expenses',data:[3.0,3.1,3.2,3.3,3.4,3.6].map(function(x){return x*1000;}),color:'#ef4444'}]));
      init('c_salesTrend', line(M.slice(0,6),[{name:'Revenue',data:[3600,3900,3700,4200,4400,5000],color:'#3b82f6',area:true}]));
      init('c_revDist', pie([{name:'Totes',value:8240},{name:'Mugs',value:6460},{name:'Candles',value:4720},{name:'Cards',value:2040},{name:'Pins',value:1280},{name:'Stickers',value:960}],true));
      init('c_purchTrend', line(M.slice(0,6),[{name:'Expenses',data:[3000,3100,3200,3300,3400,3600],color:'#ef4444',area:true}]));
      init('c_expDist', pie([{name:'Cost of goods',value:13400},{name:'Ads',value:4460},{name:'Fees',value:2230},{name:'Shipping',value:1200},{name:'Other',value:800}],true));
    },
    products:function(){ init('c_prodTrend', line(M.slice(0,6),[{name:'Revenue',data:[1180,1260,1240,1360,1420,1780],color:'#3b82f6',area:true}])); },
    geographic:function(){
      init('c_cOrigin', pie([{name:'China',value:46},{name:'Canada',value:22},{name:'USA',value:18},{name:'Vietnam',value:9},{name:'Other',value:5}]));
      init('c_compOrigin', pie([{name:'Pak Supplies',value:38},{name:'Northwind',value:27},{name:'Acme Co.',value:20},{name:'Other',value:15}]));
      init('c_cDest', pie([{name:'USA',value:52},{name:'Canada',value:28},{name:'UK',value:11},{name:'Australia',value:9}]));
      init('c_compDest', pie([{name:'Riverside Co.',value:31},{name:'Maple Retail',value:24},{name:'Bianchi Ltd',value:18},{name:'Other',value:27}]));
      geoMap();
    },
    performance:function(){
      init('c_avgTxn', bars(M.slice(0,6),[{name:'Avg value',data:[17.2,18.1,18.6,19.0,19.4,20.1],color:'#3b82f6'}]));
      init('c_totalTxn', bars(M.slice(0,6),[{name:'Transactions',data:[180,196,205,214,228,240],color:'#8b5cf6'}]));
      init('c_shipping', line(M.slice(0,6),[{name:'Avg shipping',data:[5.2,5.0,4.9,4.8,4.85,4.7],color:'#06b6d4',area:true}]));
    },
    customers:function(){
      init('c_topCust', pie([{name:'Riverside Co.',value:2100},{name:'Maple Retail',value:1640},{name:'A. Whitfield',value:1180},{name:'J. Okafor',value:920},{name:'Other',value:3200}],true));
      init('c_payStatus', pie([{name:'Paid',value:74},{name:'Partial',value:14},{name:'Unpaid',value:12}]));
      init('c_custGrowth', line(M.slice(0,6),[{name:'New customers',data:[8,11,9,13,12,15],color:'#10b981',area:true}]));
      init('c_clv', bars(M.slice(0,6),[{name:'CLV',data:[42,45,47,49,50,53],color:'#3b82f6'}]));
      init('c_activeInactive', pie([{name:'Active',value:71},{name:'Inactive',value:29}]));
      init('c_rentalsPer', bars(['0','1','2','3','4+'],[{name:'Customers',data:[310,92,46,24,14],color:'#14b8a6'}]));
    },
    taxes:function(){
      init('c_taxVsPaid', bars(M.slice(0,6),[{name:'Collected',data:[480,520,500,560,580,584],color:'#10b981'},{name:'Paid',data:[260,280,290,300,300,312],color:'#ef4444'}]));
      init('c_taxRate', bars(['0%','5%','8%','13%','15%'],[{name:'Transactions',data:[120,340,210,470,144],color:'#8b5cf6'}]));
      init('c_taxLiab', line(M.slice(0,6),[{name:'Net liability',data:[220,240,210,260,280,272],color:'#3b82f6',area:true}]));
      init('c_taxCat', pie([{name:'Sales',value:58},{name:'Supplies',value:20},{name:'Shipping',value:12},{name:'Other',value:10}]));
      init('c_taxProd', pie([{name:'Totes',value:34},{name:'Mugs',value:26},{name:'Candles',value:20},{name:'Other',value:20}]));
      init('c_expRevTax', bars(M.slice(0,6),[{name:'Revenue tax',data:[480,520,500,560,580,584],color:'#3b82f6'},{name:'Expense tax',data:[260,280,290,300,300,312],color:'#f59e0b'}]));
    },
    returns:function(){
      init('c_retTime', bars(M.slice(0,6),[{name:'Returns',data:[5,7,6,8,6,6],color:'#f59e0b'}]));
      init('c_retReasons', pie([{name:'Damaged',value:37},{name:'Wrong item',value:24},{name:'Changed mind',value:21},{name:'Late',value:18}]));
      init('c_retImpact', bars(M.slice(0,6),[{name:'$ impact',data:[160,220,180,240,170,150],color:'#ef4444'}]));
      init('c_retCat', pie([{name:'Stationery',value:42},{name:'Drinkware',value:30},{name:'Apparel',value:18},{name:'Other',value:10}]));
      init('c_retProd', pie([{name:'Stickers',value:34},{name:'Pins',value:30},{name:'Mugs',value:20},{name:'Other',value:16}]));
      init('c_retPurchSale', bars(M.slice(0,6),[{name:'Sale returns',data:[120,160,130,170,120,110],color:'#3b82f6'},{name:'Purchase returns',data:[40,60,50,70,50,40],color:'#f59e0b'}]));
    },
    losses:function(){
      init('c_lossTime', bars(M.slice(0,6),[{name:'$ losses',data:[240,300,210,280,330,280],color:'#ef4444'}]));
      init('c_lossReasons', pie([{name:'Damaged',value:44},{name:'Spoilage',value:26},{name:'Theft',value:18},{name:'Misc',value:12}]));
      init('c_lossImpact', line(M.slice(0,6),[{name:'$ impact',data:[240,300,210,280,330,280],color:'#f59e0b',area:true}]));
      init('c_lossCat', pie([{name:'Drinkware',value:38},{name:'Candles',value:28},{name:'Stationery',value:22},{name:'Other',value:12}]));
      init('c_lossProd', pie([{name:'Candles',value:40},{name:'Mugs',value:30},{name:'Totes',value:18},{name:'Other',value:12}]));
      init('c_lossPurchSale', bars(M.slice(0,6),[{name:'Sale losses',data:[140,180,120,160,200,170],color:'#3b82f6'},{name:'Purchase losses',data:[100,120,90,120,130,110],color:'#f59e0b'}]));
    },
    refunds:function(){ init('c_refundMonth', bars(M,[{name:'Refunds',data:[90,120,80,140,110,150,130,160,120,180,140,170],color:'#ef4444'}])); }
  };

  // ---------- world map ----------
  var mapReady=false, mapPending=false;
  function geoMap(){
    var el=document.getElementById('c_geoMap'); if(!el||charts.geoMap)return;
    function draw(){
      var c=echarts.init(el,null,{renderer:'svg'}); charts.geoMap=c; var t=TT();
      c.setOption(base({ tooltip:tip({trigger:'item',formatter:function(p){return p.name+(p.value?': $'+p.value.toLocaleString():': —');}}),
        visualMap:{left:14,bottom:14,min:0,max:9000,calculable:true,inRange:{color:dark?['#1e3356','#2f6fd0','#7db4ff']:['#dbeafe','#60a5fa','#1e40af']},text:['High','Low'],textStyle:{color:t.axis}},
        series:[{type:'map',map:'world',roam:false,itemStyle:{areaColor:dark?'#16223c':'#eef2f7',borderColor:dark?'rgba(255,255,255,.10)':'#dbe2ec'},emphasis:{itemStyle:{areaColor:'#10b981'},label:{show:false}},
          data:[{name:'United States',value:8600},{name:'Canada',value:5200},{name:'United Kingdom',value:2100},{name:'Australia',value:1700},{name:'Germany',value:900}]}] }));
    }
    if(mapReady){ draw(); return; }
    if(mapPending)return; mapPending=true;
    fetch(ASSETS + 'world.json')
      .then(function(r){return r.json();})
      .then(function(geo){ echarts.registerMap('world',geo); mapReady=true; draw(); })
      .catch(function(){ el.innerHTML='<div style="padding:30px;text-align:center;color:#94a3b8;font-size:13px">World map could not load.</div>'; });
  }

  // ---------- tabs ----------
  function show(name){
    curTab=name;
    document.querySelectorAll('.tabbtn').forEach(function(b){ b.classList.toggle('active', b.dataset.tab===name); });
    document.querySelectorAll('.panel').forEach(function(p){ p.classList.toggle('active', p.dataset.panel===name); });
    if(builders[name] && !built[name]){ built[name]=true; builders[name](); }
    Object.keys(charts).forEach(function(k){ charts[k].resize(); });
  }
  document.getElementById('tabbar').addEventListener('click', function(e){ var b=e.target.closest('.tabbtn'); if(b) show(b.dataset.tab); });
  show('dashboard');

  // dark-theme toggle: flip the attribute and re-render the active tab's charts
  var tbtn=document.getElementById('themeToggle');
  if(tbtn){ tbtn.addEventListener('click', function(){
    dark=!dark;
    document.documentElement.setAttribute('data-theme', dark?'dark':'light');
    tbtn.setAttribute('aria-pressed', dark);
    Object.keys(charts).forEach(function(k){ charts[k].dispose(); delete charts[k]; });
    Object.keys(built).forEach(function(k){ delete built[k]; });
    built[curTab]=true; builders[curTab]();
    Object.keys(charts).forEach(function(k){ charts[k].resize(); });
  }); }

  // ---------- cleaned-table tabs ----------
  var ctabs=document.querySelectorAll('.cleanbar .ctab');
  var rows=Array.prototype.slice.call(document.querySelectorAll('.cleantable tbody tr'));
  var label=document.getElementById('rowcount');
  ctabs.forEach(function(t){ t.addEventListener('click', function(){
    ctabs.forEach(function(x){x.classList.remove('active');}); t.classList.add('active');
    var f=t.dataset.filter, n=0;
    rows.forEach(function(r){ var m=(f==='all')||(r.dataset.type===f); r.style.display=m?'':'none'; if(m)n++; });
    if(label) label.textContent=(f==='all')?(n+' of 248 rows shown'):(n+' '+f+' rows shown');
  }); });

  window.addEventListener('resize', function(){ Object.keys(charts).forEach(function(k){ charts[k].resize(); }); });
})();
