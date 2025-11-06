document.addEventListener('DOMContentLoaded', () => {
    const buttons = document.querySelectorAll('.ticket-btn');
    const modal = document.getElementById('result-modal');
    const closeButton = document.querySelector('.close-button');
    const ticketCategoryText = document.getElementById('ticket-category-text');
    const ticketNumberText = document.getElementById('ticket-number-text');

    buttons.forEach(button => {
        button.addEventListener('click', async () => {
            const category = button.dataset.category;
            
            // เตรียมข้อมูลที่จะส่งไปให้ PHP
            const formData = new URLSearchParams();
            formData.append('category', category);

            try {
                // ส่ง request ไปยัง get_ticket.php
                const response = await fetch('api/get_ticket.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    // แสดงผลลัพธ์ใน Modal
                    ticketCategoryText.textContent = result.category.toUpperCase();
                    ticketNumberText.textContent = result.ticket_number;
                    modal.style.display = 'block'; // แสดง Modal
                } else {
                    alert('เกิดข้อผิดพลาด: ' + result.message);
                }

            } catch (error) {
                console.error('Error:', error);
                alert('ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้');
            }
        });
    });

    // ฟังก์ชันปิด Modal
    const closeModal = () => {
        modal.style.display = 'none';
    };

    closeButton.addEventListener('click', closeModal);

    // ปิด Modal เมื่อคลิกพื้นที่สีเทาด้านนอก
    window.addEventListener('click', (event) => {
        if (event.target === modal) {
            closeModal();
        }
    });
});