// Orodha ya picha zinazobadilika kwenye Home page (Hakikisha zipo kwenye folda yako ya images)
const sliderImages = [
    'images/gallery1.jpg',
    'images/gallery2.jpg',
    'images/gallery3.jpg'
];

let currentImageIndex = 0;
const homeSection = document.getElementById('home');

function changeHomeBackground() {
    if (homeSection) {
        // Badilisha background image moja kwa moja
        homeSection.style.backgroundImage = `linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.4)), url('${sliderImages[currentImageIndex]}')`;
        homeSection.style.backgroundSize = 'cover';
        homeSection.style.backgroundPosition = 'center';
        
        // Nenda picha inayofuata, ikifika mwisho irudi mwanzo
        currentImageIndex = (currentImageIndex + 1) % sliderImages.length;
    }
}

// Badilisha picha kila baada ya sekunde 4
setInterval(changeHomeBackground, 4000);

// Ianze mara moja ukurasa unapofunguka
window.onload = function() {
    changeHomeBackground();
};
        // 8. Dynamic Content
        const destData = [
            { name: "Stone Town", img: "images/stone.jpg", price: "250,000", desc: "Heritage and ancient history." },
            { name: "Nungwi Beach", img: "images/nungwi.jpg", price: "200,000", desc: "Sunsets and white sand." },
            { name: "Jozani Forest", img: "images/nyani.jpg", price: "150,000", desc: "Rare Red Colobus Monkeys." }
        ];

        // 10. Unique Feature: Typing Animation for Home
        const textToType = "Start Your Journey...";
        let charIndex = 0;
        function typeEffect() {
            const target = document.getElementById("typing-text");
            if (charIndex < textToType.length) {
                target.innerHTML += textToType.charAt(charIndex);
                charIndex++;
                setTimeout(typeEffect, 500);
            }
        }

        window.onload = () => {
            typeEffect();
            const container = document.getElementById('dest-list');
            destData.forEach(item => {
                container.innerHTML += `
                    <div class="card">
                        <img src="${item.img}" onerror="this.src='https://via.placeholder.com/300'">
                        <div style="padding:15px;">
                            <h3>${item.name}</h3>
                            <p>${item.desc}</p>
                            <strong>Tsh ${item.price}</strong>
                        </div>
                    </div>`;
            });
        };

        function switchPage(pageId) {
            document.querySelectorAll('.section-box').forEach(sec => sec.classList.remove('active-section'));
            document.getElementById(pageId).classList.add('active-section');
        }

        function calculate() {
            const p = parseInt(document.getElementById('calcDest').value);
            const n = parseInt(document.getElementById('calcPeople').value);
            let total = p * n;
            // 4. Conditional Statement
            if(n >= 5) total = total * 0.9; 
            document.getElementById('totalVal').innerText = total.toLocaleString();
        }

        function checkStrength(val) {
            const label = document.getElementById('password-strength');
            label.innerText = val.length < 6 ? "Weak" : "Strong";
            label.style.color = val.length < 6 ? "red" : "green";
        }

        function toggleDark() { document.body.classList.toggle('dark-mode'); }
        function handleAuth(e) { e.preventDefault(); alert("Successful!"); }
