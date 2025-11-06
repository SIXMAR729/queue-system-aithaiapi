document.addEventListener('DOMContentLoaded', () => {
    const callButtons = document.querySelectorAll('.call-btn');
    const resetButton = document.getElementById('reset-btn');

    // ✨ **1. ฟังก์ชันใหม่สำหรับเรียก API และเล่นเสียง**
    async function playAudioFromApi(text) {
        try {
            // ส่งข้อความไปให้ generate_speech.php
            const response = await fetch('generate_speech.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ text: text })
            });

            const result = await response.json();

            if (result.success && result.audioUrl) {
                // ถ้าสำเร็จ สร้าง element เสียงแล้วสั่งเล่น
                const audio = new Audio(result.audioUrl);
                audio.play();

                // (ตัวเลือกเสริม) ลบไฟล์เสียงหลังจากเล่นเสร็จเพื่อไม่ให้รกเซิร์ฟเวอร์
                audio.onended = () => {
                    // เราจะสร้างฟังก์ชันลบไฟล์ในภายหลังถ้าต้องการ
                    console.log('Finished playing:', result.audioUrl);
                };

            } else {
                console.error('Failed to generate speech:', result.message);
                alert('เกิดข้อผิดพลาดในการสร้างเสียงพูด: ' + result.message);
            }
        } catch (error) {
            console.error('Error calling speech API:', error);
        }
    }


    // ฟังก์ชันสำหรับเรียกคิว (มีการปรับปรุง)
    async function callNextTicket(category, categoryText) {
        const formData = new URLSearchParams();
        formData.append('category', category);

        try {
            const response = await fetch('call_next.php', {
                method: 'POST',
                body: formData
            });
            const result = await response.json();

            if (result.success) {
                console.log(`Called ticket ${result.called_number} for category ${category}`);
                
                // ✨ **2. เปลี่ยนมาเรียกใช้ฟังก์ชันใหม่**
                const textToSpeak = `ขอเชิญหมายเลข ${result.called_number}, ${categoryText}, ที่ช่องบริการค่ะ`;
                playAudioFromApi(textToSpeak);

                updateDisplay(); // อัปเดตหน้าจอทันที
            } else {
                alert(`ไม่สามารถเรียกคิวได้: ${result.message}`);
            }
        } catch (error) {
            console.error('Error calling next ticket:', error);
        }
    }


    async function updateDisplay() {
        try {
            const response = await fetch('display_data.php');
            const data = await response.json();
            for (const category in data) {
                const info = data[category];
                document.getElementById(`current-${category}`).textContent = info.current || 0;
                document.getElementById(`next-${category}`).textContent = info.next || '-';
                document.getElementById(`waiting-${category}`).textContent = info.waiting;
                const callBtn = document.querySelector(`.card[data-category="${category}"] .call-btn`);
                callBtn.disabled = !info.next;
            }
        } catch (error) {
            console.error('Failed to fetch queue data:', error);
        }
    }
    
    callButtons.forEach(button => {
        button.addEventListener('click', () => {
            const card = button.closest('.card');
            const category = card.dataset.category;
            const categoryText = card.querySelector('h2').textContent.split('(')[0].trim();
            callNextTicket(category, categoryText);
        });
    });

    resetButton.addEventListener('click', async () => {
        const isConfirmed = confirm('คุณแน่ใจหรือไม่ว่าต้องการรีเซ็ตคิวทั้งหมด? การกระทำนี้ไม่สามารถย้อนกลับได้');
        if (isConfirmed) {
            try {
                const response = await fetch('reset_queue.php', { method: 'POST' });
                const result = await response.json();
                if (result.success) {
                    alert('รีเซ็ตคิวทั้งหมดเรียบร้อยแล้ว');
                    updateDisplay();
                } else {
                    alert('เกิดข้อผิดพลาดในการรีเซ็ต: ' + result.message);
                }
            } catch (error) {
                console.error('Error resetting queue:', error);
            }
        }
    });

    updateDisplay();
    setInterval(updateDisplay, 5000);
});