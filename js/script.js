// Check authentication on page load
async function checkAuth() {
  try {
    const response = await fetch('backend/check-auth.php', { credentials: 'include' });
    const data = await response.json();
    
    if (!data.success) {
      window.location.href = 'login.html';
    }
  } catch (error) {
    console.error('Auth check failed:', error);
    window.location.href = 'login.html';
  }
}

// Logout function
async function logout() {
  try {
    await fetch('backend/logout.php', { credentials: 'include' });
    window.location.href = 'login.html';
  } catch (error) {
    console.error('Logout failed:', error);
  }
}

function showSection(id){
    document.querySelectorAll('section').forEach(s=>s.classList.remove('active'));
    document.getElementById(id).classList.add('active');
    document.querySelectorAll('#menu button').forEach(b=>b.classList.remove('active'));
    const map = {chobi:'m-chobi',kobita:'m-kobita',puroshkar:'m-puroshkar',deyalikha:'m-deyalikha'};
    document.getElementById(map[id]).classList.add('active');
  }
  // ডিফল্ট
  showSection('chobi');

  // Gallery handling (PHP Backend)
  const galleryEl = document.getElementById('gallery');
  const input = document.getElementById('imageInput');
  
  // New Upload UI Logic
  const imageInput = document.getElementById('imageInput');
  const fileNameDisplay = document.getElementById('fileName');
  const uploadBtn = document.getElementById('uploadBtn');
  let selectedFile = null;

  imageInput.addEventListener('change', (e) => {
    if (e.target.files && e.target.files.length > 0) {
      selectedFile = e.target.files[0];
      fileNameDisplay.textContent = selectedFile.name;
      uploadBtn.style.display = 'inline-block';
    } else {
      selectedFile = null;
      fileNameDisplay.textContent = 'কোন ছবি নির্বাচন করা হয়নি';
      uploadBtn.style.display = 'none';
    }
  });

  uploadBtn.addEventListener('click', async () => {
    if (!selectedFile) return;

    const originalText = uploadBtn.textContent;
    uploadBtn.textContent = 'আপলোড হচ্ছে...';
    uploadBtn.disabled = true;

    try {
      // Compress image before upload (max 800px width, 0.7 quality)
      // We need aggressive compression for DB storage to avoid packet size limits
      const compressedData = await compressImage(selectedFile, 800, 0.7);
      
      // Check size of base64 string
      if (compressedData.length > 5 * 1024 * 1024) {
         throw new Error("ছবির সাইজ অনেক বেশি। অনুগ্রহ করে ছোট ছবি ব্যবহার করুন।");
      }

      const success = await addImageToGallery(compressedData);
      
      if (!success) return;
      
      // Reset UI
      imageInput.value = '';
      selectedFile = null;
      fileNameDisplay.textContent = 'কোন ছবি নির্বাচন করা হয়নি';
      uploadBtn.style.display = 'none';
      alert('ছবি সফলভাবে আপলোড হয়েছে!');
    } catch (err) {
      console.error("Upload error:", err);
      alert("ছবি আপলোড করতে সমস্যা হয়েছে: " + (err.message || err));
    } finally {
      uploadBtn.textContent = originalText;
      uploadBtn.disabled = false;
    }
  });
  
  function fileToDataURL(file){
    return new Promise((res,rej)=>{
      const fr=new FileReader();
      fr.onload = ()=>res(fr.result);
      fr.onerror = rej;
      fr.readAsDataURL(file);
    })
  }

  // Image compression helper
  function compressImage(file, maxWidth, quality) {
    return new Promise((resolve, reject) => {
      const reader = new FileReader();
      reader.readAsDataURL(file);
      reader.onload = event => {
        const img = new Image();
        img.src = event.target.result;
        img.onload = () => {
          const canvas = document.createElement('canvas');
          let width = img.width;
          let height = img.height;

          if (width > maxWidth) {
            height *= maxWidth / width;
            width = maxWidth;
          }

          canvas.width = width;
          canvas.height = height;
          const ctx = canvas.getContext('2d');
          ctx.drawImage(img, 0, 0, width, height);
          
          // Get compressed base64 string
          const dataUrl = canvas.toDataURL('image/jpeg', quality);
          resolve(dataUrl);
        };
        img.onerror = error => reject(error);
      };
      reader.onerror = error => reject(error);
    });
  }
  
  async function addImageToGallery(dataURL){
    try {
      const response = await fetch('backend/api/gallery.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        credentials: 'include', // IMPORTANT: Send session cookie
        body: JSON.stringify({ image_data: dataURL })
      });

      const text = await response.text();
      let data;
      try {
        data = JSON.parse(text);
      } catch (e) {
        console.error("Server returned non-JSON:", text);
        throw new Error("Server Error (" + response.status + "): " + text.substring(0, 100));
      }
      
      if (data.success) {
        await loadGalleryToUI();
        return true;
      } else {
        console.error("Server error:", data);
        alert('ছবি যোগ করতে সমস্যা হয়েছে: ' + (data.message || 'Unknown error'));
        return false;
      }
    } catch (error) {
      console.error('Error adding image:', error);
      alert('ছবি যোগ করতে সমস্যা হয়েছে: ' + (error.message || 'Server connection failed'));
      return false;
    }
  }
  
  async function loadGalleryToUI(){
    try {
      const response = await fetch('backend/api/gallery.php', { credentials: 'include' });
      const data = await response.json();
      
      if (data.success) {
        galleryEl.innerHTML = '';
        
        data.data.forEach((item) => {
          const wrapper = document.createElement('div');
          wrapper.className = 'gallery-item';
          
          const img = document.createElement('img');
          img.src = item.image_data;
          img.onclick = () => openLightbox(item.image_data);
          
          const deleteBtn = document.createElement('button');
          deleteBtn.innerHTML = '&times;';
          deleteBtn.className = 'poem-delete';
          deleteBtn.title = 'Remove photo';
          deleteBtn.onclick = (e) => {
            e.stopPropagation();
            if(confirm('ছবি মুছে ফেলবেন?')){
              deleteGalleryImage(item.id);
            }
          };
          
          wrapper.appendChild(img);
          wrapper.appendChild(deleteBtn);
          galleryEl.appendChild(wrapper);
        });
      }
    } catch (error) {
      console.error('Error loading gallery:', error);
    }
  }
  
  async function deleteGalleryImage(id){
    try {
      const response = await fetch('backend/api/gallery.php', {
        method: 'DELETE',
        headers: {
          'Content-Type': 'application/json'
        },
        credentials: 'include',
        body: JSON.stringify({ id: id })
      });
      
      const data = await response.json();
      
      if (data.success) {
        await loadGalleryToUI();
      } else {
        alert('ছবি মুছতে সমস্যা হয়েছে।');
      }
    } catch (error) {
      console.error('Error deleting image:', error);
      alert('ছবি মুছতে সমস্যা হয়েছে।');
    }
  }

  // Lightbox functions
  function openLightbox(imgSrc){
    document.getElementById('lightbox').classList.add('active');
    document.getElementById('lightbox-img').src = imgSrc;
  }
  
  function closeLightbox(){
    document.getElementById('lightbox').classList.remove('active');
  }
  
  // Close lightbox on background click
  document.getElementById('lightbox').addEventListener('click', (e)=>{
    if(e.target.id === 'lightbox'){
      closeLightbox();
    }
  });

  // Poems (PHP Backend)
  async function savePoem(){
    const t = document.getElementById('poemTitle').value.trim();
    const b = document.getElementById('poemBody').value.trim();
    
    if(!b) return alert('কবিতা বা স্মৃতিকথা টেক্সট দিন');
    
    try {
      const response = await fetch('backend/api/kobita.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        credentials: 'include',
        body: JSON.stringify({ title: t, body: b })
      });
      
      const data = await response.json();
      
      if (data.success) {
        document.getElementById('poemTitle').value = ''; 
        document.getElementById('poemBody').value = '';
        await renderPoems();
      } else {
        alert('কবিতা যোগ করতে সমস্যা হয়েছে।');
      }
    } catch (error) {
      console.error('Error saving poem:', error);
      alert('কবিতা যোগ করতে সমস্যা হয়েছে।');
    }
  }
  
  async function renderPoems(){
    try {
      const response = await fetch('backend/api/kobita.php', { credentials: 'include' });
      const data = await response.json();
      
      const el = document.getElementById('poemList');
      el.innerHTML = '';
      
      if (data.success && data.data.length > 0) {
        data.data.forEach((p) => {
          const d = document.createElement('div');
          d.className = 'poem';
          
          const h = document.createElement('strong');
          h.textContent = p.title;
          d.appendChild(h);
          
          const when = document.createElement('div');
          when.style.fontSize = '0.8rem';
          when.style.color = '#666';
          when.textContent = new Date(p.when).toLocaleString();
          d.appendChild(when);
          
          const br = document.createElement('div');
          br.style.marginTop = '0.5rem';
          br.style.whiteSpace = 'pre-wrap';
          br.textContent = p.body;
          d.appendChild(br);
          
          const deleteBtn = document.createElement('button');
          deleteBtn.textContent = 'মুছুন';
          deleteBtn.className = 'poem-delete';
          deleteBtn.onclick = () => {
            if(confirm('এই কবিতা/স্মৃতিকথা মুছে ফেলবেন?')){
              deletePoem(p.id);
            }
          };
          d.appendChild(deleteBtn);
          
          el.appendChild(d);
        });
      } else {
        el.innerHTML = '<p style="color:#666">কোনো কবিতা বা স্মৃতিকথা নেই।</p>';
      }
    } catch (error) {
      console.error('Error loading poems:', error);
    }
  }
  
  async function deletePoem(id){
    try {
      const response = await fetch('backend/api/kobita.php', {
        method: 'DELETE',
        headers: {
          'Content-Type': 'application/json'
        },
        credentials: 'include',
        body: JSON.stringify({ id: id })
      });
      
      const data = await response.json();
      
      if (data.success) {
        await renderPoems();
      } else {
        alert('কবিতা মুছতে সমস্যা হয়েছে।');
      }
    } catch (error) {
      console.error('Error deleting poem:', error);
      alert('কবিতা মুছতে সমস্যা হয়েছে।');
    }
  }

  // Awards (PHP Backend)
  async function addAward(){
    const n = document.getElementById('awardName').value.trim();
    const d = document.getElementById('awardDesc').value.trim();
    
    if(!n) return alert('পুরস্কারের নাম দিন');
    
    try {
      const response = await fetch('backend/api/awards.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        credentials: 'include',
        body: JSON.stringify({ name: n, desc: d })
      });
      
      const data = await response.json();
      
      if (data.success) {
        document.getElementById('awardName').value = '';
        document.getElementById('awardDesc').value = '';
        await renderAwards();
      } else {
        alert('পুরস্কার যোগ করতে সমস্যা হয়েছে।');
      }
    } catch (error) {
      console.error('Error adding award:', error);
      alert('পুরস্কার যোগ করতে সমস্যা হয়েছে।');
    }
  }
  
  async function renderAwards(){
    try {
      const response = await fetch('backend/api/awards.php', { credentials: 'include' });
      const data = await response.json();
      
      const el = document.getElementById('awardList');
      el.innerHTML = '';
      
      if (data.success && data.data.length > 0) {
        data.data.forEach((a) => {
          const d = document.createElement('div');
          d.className = 'poem';
          d.innerHTML = '<strong>' + a.name + '</strong><div style="font-size:0.9rem;color:#444">' + (a.desc || '') + '</div>';
          
          const deleteBtn = document.createElement('button');
          deleteBtn.textContent = 'মুছুন';
          deleteBtn.className = 'poem-delete';
          deleteBtn.onclick = () => {
            if(confirm('এই পুরস্কার মুছে ফেলবেন?')){
              deleteAward(a.id);
            }
          };
          d.appendChild(deleteBtn);
          
          el.appendChild(d);
        });
      } else {
        el.innerHTML = '<p style="color:#666">কোনো পুরুস্কার তালিকায় নেই।</p>';
      }
    } catch (error) {
      console.error('Error loading awards:', error);
    }
  }
  
  async function deleteAward(id){
    try {
      const response = await fetch('backend/api/awards.php', {
        method: 'DELETE',
        headers: {
          'Content-Type': 'application/json'
        },
        credentials: 'include',
        body: JSON.stringify({ id: id })
      });
      
      const data = await response.json();
      
      if (data.success) {
        await renderAwards();
      } else {
        alert('পুরস্কার মুছতে সমস্যা হয়েছে।');
      }
    } catch (error) {
      console.error('Error deleting award:', error);
      alert('পুরস্কার মুছতে সমস্যা হয়েছে।');
    }
  }

  // Guestbook (PHP Backend)
  async function addGuestbook(){
    const n = document.getElementById('gbName').value.trim();
    const m = document.getElementById('gbMessage').value.trim();
    
    if(!m) return alert('বার্তা লিখুন');
    
    try {
      const response = await fetch('backend/api/deyalikha.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        credentials: 'include',
        body: JSON.stringify({ name: n, message: m })
      });
      
      const data = await response.json();
      
      if (data.success) {
        document.getElementById('gbName').value = '';
        document.getElementById('gbMessage').value = '';
        await renderGuestbook();
      } else {
        alert('বার্তা যোগ করতে সমস্যা হয়েছে।');
      }
    } catch (error) {
      console.error('Error adding guestbook entry:', error);
      alert('বার্তা যোগ করতে সমস্যা হয়েছে।');
    }
  }
  
  async function renderGuestbook(){
    try {
      const response = await fetch('backend/api/deyalikha.php', { credentials: 'include' });
      const data = await response.json();
      
      const el = document.getElementById('gbList');
      el.innerHTML = '';
      
      if (data.success && data.data.length > 0) {
        data.data.forEach((g) => {
          const d = document.createElement('div');
          d.className = 'poem';
          d.innerHTML = '<strong>' + g.name + '</strong><div style="font-size:0.8rem;color:#666">' + new Date(g.when).toLocaleString() + '</div><div style="margin-top:0.5rem;white-space:pre-wrap">' + g.msg + '</div>';
          
          const deleteBtn = document.createElement('button');
          deleteBtn.textContent = 'মুছুন';
          deleteBtn.className = 'poem-delete';
          deleteBtn.onclick = () => {
            if(confirm('এই বার্তা মুছে ফেলবেন?')){
              deleteGuestbook(g.id);
            }
          };
          d.appendChild(deleteBtn);
          
          el.appendChild(d);
        });
      } else {
        el.innerHTML = '<p style="color:#666">কেউ এখনও বার্তা দেয়নি।</p>';
      }
    } catch (error) {
      console.error('Error loading guestbook:', error);
    }
  }
  
  async function deleteGuestbook(id){
    try {
      const response = await fetch('backend/api/deyalikha.php', {
        method: 'DELETE',
        headers: {
          'Content-Type': 'application/json'
        },
        credentials: 'include',
        body: JSON.stringify({ id: id })
      });
      
      const data = await response.json();
      
      if (data.success) {
        await renderGuestbook();
      } else {
        alert('বার্তা মুছতে সমস্যা হয়েছে।');
      }
    } catch (error) {
      console.error('Error deleting guestbook entry:', error);
      alert('বার্তা মুছতে সমস্যা হয়েছে।');
    }
  }

  // Clear All (now clears database)
  async function clearAll(){ 
    if(!confirm('সত্যিই সব ডেটা মুছতে চান?')) return;
    
    try {
      // Delete all from each table
      await Promise.all([
        fetch('backend/api/gallery.php', { method: 'DELETE', headers: {'Content-Type': 'application/json'}, credentials: 'include', body: JSON.stringify({id: 0}) }),
        fetch('backend/api/kobita.php', { method: 'DELETE', headers: {'Content-Type': 'application/json'}, credentials: 'include', body: JSON.stringify({id: 0}) }),
        fetch('backend/api/awards.php', { method: 'DELETE', headers: {'Content-Type': 'application/json'}, credentials: 'include', body: JSON.stringify({id: 0}) }),
        fetch('backend/api/deyalikha.php', { method: 'DELETE', headers: {'Content-Type': 'application/json'}, credentials: 'include', body: JSON.stringify({id: 0}) })
      ]);
      
      location.reload();
    } catch (error) {
      console.error('Error clearing data:', error);
      alert('ডেটা মুছতে সমস্যা হয়েছে।');
    }
  }

  // Initialize - Check auth and load data
  window.addEventListener('load', async () => { 
    await checkAuth();
    await loadGalleryToUI(); 
    await renderPoems(); 
    await renderAwards(); 
    await renderGuestbook(); 
  });


