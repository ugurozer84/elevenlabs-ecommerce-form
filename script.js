document.addEventListener('DOMContentLoaded', function() {
    const phoneInput = document.getElementById('phoneNumber');
    const callButton = document.getElementById('callButton');
    const callStatus = document.getElementById('callStatus');
    const buttonText = document.querySelector('.button-text');
    const loadingSpinner = document.querySelector('.loading-spinner');

    // Telefon numarası formatlaması
    phoneInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, ''); // Sadece rakamları al
        
        // 10 haneli Türk telefon numarası kontrolü
        if (value.length > 10) {
            value = value.slice(0, 10);
        }
        
        // Format: 5XX XXX XX XX
        if (value.length >= 3) {
            value = value.replace(/(\d{3})(\d{3})(\d{2})(\d{2})/, '$1 $2 $3 $4');
        }
        
        e.target.value = value;
        
        // Buton durumunu güncelle
        updateButtonState();
    });

    // Sadece rakam girişine izin ver
    phoneInput.addEventListener('keypress', function(e) {
        if (!/\d/.test(e.key) && !['Backspace', 'Delete', 'Tab', 'Enter'].includes(e.key)) {
            e.preventDefault();
        }
    });

    // Buton durumunu güncelle
    function updateButtonState() {
        const phoneValue = phoneInput.value.replace(/\D/g, '');
        const isValidPhone = phoneValue.length === 10 && phoneValue.startsWith('5');
        
        callButton.disabled = !isValidPhone;
        
        if (isValidPhone) {
            callButton.style.background = 'linear-gradient(135deg, #48bb78 0%, #38a169 100%)';
        } else {
            callButton.style.background = '#cbd5e0';
        }
    }

    // Arama butonuna tıklama
    callButton.addEventListener('click', async function() {
        const phoneValue = phoneInput.value.replace(/\D/g, '');
        
        if (phoneValue.length !== 10 || !phoneValue.startsWith('5')) {
            showStatus('Lütfen geçerli bir telefon numarası girin (5XX XXX XX XX)', 'error');
            return;
        }

        // Buton durumunu güncelle
        callButton.disabled = true;
        callButton.classList.add('calling');
        buttonText.style.display = 'none';
        loadingSpinner.style.display = 'block';
        
        showStatus('Arama başlatılıyor...', 'info');

        try {
            // Backend'e arama isteği gönder
            const response = await fetch('/api/call', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    phoneNumber: '+90' + phoneValue
                })
            });

            const result = await response.json();

            if (response.ok) {
                showStatus('✅ Arama başarıyla başlatıldı! AI asistanımız sizi kısa süre içinde arayacak.', 'success');
                
                // Formu temizle
                setTimeout(() => {
                    phoneInput.value = '';
                    updateButtonState();
                    hideStatus();
                }, 5000);
            } else {
                throw new Error(result.error || 'Arama başlatılamadı');
            }
        } catch (error) {
            console.error('Arama hatası:', error);
            showStatus('❌ Arama başlatılırken bir hata oluştu. Lütfen tekrar deneyin.', 'error');
        } finally {
            // Buton durumunu eski haline getir
            setTimeout(() => {
                callButton.disabled = false;
                callButton.classList.remove('calling');
                buttonText.style.display = 'block';
                loadingSpinner.style.display = 'none';
                updateButtonState();
            }, 2000);
        }
    });

    // Durum mesajını göster
    function showStatus(message, type) {
        callStatus.textContent = message;
        callStatus.className = `call-status ${type}`;
        callStatus.style.display = 'block';
    }

    // Durum mesajını gizle
    function hideStatus() {
        callStatus.style.display = 'none';
    }

    // Sayfa yüklendiğinde buton durumunu kontrol et
    updateButtonState();

    // Enter tuşu ile arama başlatma
    phoneInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter' && !callButton.disabled) {
            callButton.click();
        }
    });

    // Telefon numarası alanına odaklanma efekti
    phoneInput.addEventListener('focus', function() {
        this.parentElement.style.borderColor = '#667eea';
        this.parentElement.style.boxShadow = '0 0 0 3px rgba(102, 126, 234, 0.1)';
    });

    phoneInput.addEventListener('blur', function() {
        this.parentElement.style.borderColor = '#e2e8f0';
        this.parentElement.style.boxShadow = 'none';
    });
});

// Arama fonksiyonu
async function makeCall() {
    const phoneNumber = document.getElementById('phoneNumber').value;
    const callButton = document.getElementById('callButton');
    const statusDiv = document.getElementById('status');
    
    // Telefon numarası validasyonu
    if (!phoneNumber) {
        showStatus('Lütfen telefon numaranızı girin.', 'error');
        return;
    }
    
    if (!phoneNumber.match(/^\+90[0-9]{10}$/)) {
        showStatus('Telefon numarası +905XXXXXXXXX formatında olmalıdır.', 'error');
        return;
    }
    
    // Buton durumunu değiştir
    callButton.disabled = true;
    callButton.textContent = 'Arama Başlatılıyor...';
    showStatus('Arama başlatılıyor...', 'loading');
    
    try {
        const response = await fetch('/api/call', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ phoneNumber })
        });
        
        const data = await response.json();
        
        if (data.success) {
            showStatus(`✅ ${data.message}`, 'success');
            
            // Arama durumunu takip et
            if (data.batchId) {
                setTimeout(() => {
                    checkCallStatus(data.batchId);
                }, 10000); // 10 saniye sonra durum kontrol et
            }
        } else if (data.demoMode) {
            // Demo modu mesajı
            showDemoModeMessage(data);
        } else {
            showStatus(`❌ ${data.error || data.message}`, 'error');
        }
        
    } catch (error) {
        console.error('Arama hatası:', error);
        showStatus('❌ Arama başlatılırken bir hata oluştu. Lütfen tekrar deneyin.', 'error');
    } finally {
        // Buton durumunu eski haline getir
        callButton.disabled = false;
        callButton.textContent = '📞 Hemen Ara';
    }
}

// Demo modu mesajını göster
function showDemoModeMessage(data) {
    const statusDiv = document.getElementById('status');
    
    let instructionsHtml = '';
    if (data.instructions && data.instructions.length > 0) {
        instructionsHtml = '<div class="demo-instructions">';
        data.instructions.forEach(instruction => {
            instructionsHtml += `<div class="instruction-item">${instruction}</div>`;
        });
        instructionsHtml += '</div>';
    }
    
    statusDiv.innerHTML = `
        <div class="demo-mode-message">
            <div class="demo-header">
                <span class="demo-icon">🔧</span>
                <strong>${data.message}</strong>
            </div>
            <div class="demo-phone">
                Girilen numara: <strong>${data.phoneNumber}</strong>
            </div>
            ${instructionsHtml}
            <div class="demo-note">
                <small>💡 Telefon numarası entegrasyonu tamamlandıktan sonra gerçek aramalar yapılabilecek.</small>
            </div>
        </div>
    `;
    statusDiv.className = 'status demo';
}
