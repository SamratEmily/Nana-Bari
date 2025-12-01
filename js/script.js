function showSection(id){
    document.querySelectorAll('section').forEach(s=>s.classList.remove('active'));
    document.getElementById(id).classList.add('active');
    document.querySelectorAll('#menu button').forEach(b=>b.classList.remove('active'));
    const map = {chobi:'m-chobi',kobita:'m-kobita',puroshkar:'m-puroshkar',deyalikha:'m-deyalikha'};
    document.getElementById(map[id]).classList.add('active');
  }
  // ডিফল্ট
  showSection('chobi');

  // Gallery handling (localStorage)
  const galleryEl = document.getElementById('gallery');
  const input = document.getElementById('imageInput');
  input.addEventListener('change', async (e)=>{
    const files = Array.from(e.target.files);
    for(const f of files){
      const data = await fileToDataURL(f);
      addImageToGallery(data);
    }
    saveGallery();
    input.value = '';
  });
  function fileToDataURL(file){
    return new Promise((res,rej)=>{
      const fr=new FileReader();
      fr.onload = ()=>res(fr.result);
      fr.onerror = rej;
      fr.readAsDataURL(file);
    })
  }
  function addImageToGallery(dataURL){
    const img=document.createElement('img'); img.src=dataURL;
    galleryEl.prepend(img);
    // keep in memory array
    const arr = loadJSON('gallery')||[];
    arr.unshift(dataURL);
    localStorage.setItem('gallery', JSON.stringify(arr.slice(0,50))); // limit to 50
  }
  function saveGallery(){ /* already saved when adding */ }
  function loadGalleryToUI(){
    galleryEl.innerHTML='';
    const arr = loadJSON('gallery')||[];
    arr.forEach(d=>{const img=document.createElement('img');img.src=d;galleryEl.appendChild(img);});
  }

  // Poems
  function savePoem(){
    const t=document.getElementById('poemTitle').value.trim();
    const b=document.getElementById('poemBody').value.trim();
    if(!b) return alert('কবিতা বা স্মৃতিকথা টেক্সট দিন');
    const arr = loadJSON('poems')||[];
    arr.unshift({title:t||'নামহীন',body:b,when:new Date().toISOString()});
    localStorage.setItem('poems', JSON.stringify(arr));
    document.getElementById('poemTitle').value=''; document.getElementById('poemBody').value='';
    renderPoems();
  }
  function renderPoems(){
    const el = document.getElementById('poemList'); el.innerHTML='';
    const arr = loadJSON('poems')||[];
    if(arr.length===0) el.innerHTML='<p style="color:#666">কোনো কবিতা বা স্মৃতিকথা নেই।</p>';
    arr.forEach(p=>{
      const d=document.createElement('div'); d.className='poem';
      const h=document.createElement('strong'); h.textContent=p.title; d.appendChild(h);
      const when=document.createElement('div'); when.style.fontSize='0.8rem'; when.style.color='#666'; when.textContent=new Date(p.when).toLocaleString(); d.appendChild(when);
      const br=document.createElement('div'); br.style.marginTop='0.5rem'; br.textContent=p.body; d.appendChild(br);
      el.appendChild(d);
    })
  }

  // Awards
  function addAward(){
    const n=document.getElementById('awardName').value.trim();
    const d=document.getElementById('awardDesc').value.trim();
    if(!n) return alert('পুরস্কারের নাম দিন');
    const arr = loadJSON('awards')||[]; arr.unshift({name:n,desc:d}); localStorage.setItem('awards', JSON.stringify(arr));
    document.getElementById('awardName').value=''; document.getElementById('awardDesc').value=''; renderAwards();
  }
  function renderAwards(){
    const el=document.getElementById('awardList'); el.innerHTML='';
    const arr=loadJSON('awards')||[];
    if(arr.length===0) el.innerHTML='<p style="color:#666">কোনো পুরুস্কার তালিকায় নেই।</p>';
    arr.forEach(a=>{const d=document.createElement('div'); d.className='poem'; d.innerHTML='<strong>'+a.name+'</strong><div style="font-size:0.9rem;color:#444">'+(a.desc||'')+'</div>'; el.appendChild(d);})
  }

  // Guestbook
  function addGuestbook(){
    const n=document.getElementById('gbName').value.trim()||'অতিথি';
    const m=document.getElementById('gbMessage').value.trim();
    if(!m) return alert('বার্তা লিখুন');
    const arr=loadJSON('guestbook')||[]; arr.unshift({name:n,msg:m,when:new Date().toISOString()}); localStorage.setItem('guestbook', JSON.stringify(arr));
    document.getElementById('gbName').value=''; document.getElementById('gbMessage').value=''; renderGuestbook();
  }
  function renderGuestbook(){
    const el=document.getElementById('gbList'); el.innerHTML=''; const arr=loadJSON('guestbook')||[];
    if(arr.length===0) el.innerHTML='<p style="color:#666">কেউ এখনও বার্তা দেয়নি।</p>';
    arr.forEach(g=>{const d=document.createElement('div'); d.className='poem'; d.innerHTML='<strong>'+g.name+'</strong><div style="font-size:0.8rem;color:#666">'+new Date(g.when).toLocaleString()+'</div><div style="margin-top:0.5rem">'+g.msg+'</div>'; el.appendChild(d);})
  }

  // Utilities
  function loadJSON(k){try{return JSON.parse(localStorage.getItem(k));}catch(e){return null}}
  function clearAll(){ if(confirm('সত্যিই সব লোকাল ডেটা মুছতে চান?')){ localStorage.removeItem('gallery'); localStorage.removeItem('poems'); localStorage.removeItem('awards'); localStorage.removeItem('guestbook'); location.reload(); } }

  // লোড
  window.addEventListener('load',()=>{ loadGalleryToUI(); renderPoems(); renderAwards(); renderGuestbook(); });
