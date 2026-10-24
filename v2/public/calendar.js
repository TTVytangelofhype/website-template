const grid=document.querySelector('#calendar-grid');
const heading=document.querySelector('#month-label');
let visible=new Date();visible.setDate(1);
const fmt=d=>d.getFullYear()+'-'+String(d.getMonth()+1).padStart(2,'0')+'-'+String(d.getDate()).padStart(2,'0');
const esc=s=>String(s).replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
async function render(){
 const first=new Date(visible.getFullYear(),visible.getMonth(),1),last=new Date(visible.getFullYear(),visible.getMonth()+1,0);
 heading.textContent=new Intl.DateTimeFormat('en-GB',{month:'long',year:'numeric'}).format(first);
 grid.innerHTML='<p>Loading confirmed bookings…</p>';
 try{
  const response=await fetch('calendar.php?from='+fmt(first)+'&to='+fmt(last));
  if(!response.ok)throw Error('Unable to fetch schedule');
  const data=await response.json(),items=data.bookings||[];
  const offset=(first.getDay()+6)%7;
  let html=['Mon','Tue','Wed','Thu','Fri','Sat','Sun'].map(d=>'<div class="weekday">'+d+'</div>').join('');
  html+='<div class="day-empty"></div>'.repeat(offset);
  for(let n=1;n<=last.getDate();n++){
   const dt=fmt(new Date(first.getFullYear(),first.getMonth(),n));
   const list=items.filter(b=>b.start_at.slice(0,10)===dt);
   html+='<article class="day"><strong>'+n+'</strong>'+list.map(b=>'<div class="cal-entry '+(b.kind==='dj'?'is-dj':'is-event')+'"><small>'+esc(b.start_at.slice(11,16))+'</small> '+esc(b.title)+'<small> — '+esc(b.host)+'</small></div>').join('')+'</article>';
  }
  grid.innerHTML=html;
 }catch(e){grid.textContent='Schedule unavailable. Try refreshing shortly.';}
}
document.querySelector('#previous-month').addEventListener('click',()=>{visible.setMonth(visible.getMonth()-1);render()});
document.querySelector('#next-month').addEventListener('click',()=>{visible.setMonth(visible.getMonth()+1);render()});
render();