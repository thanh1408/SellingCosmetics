function openCartModal(name, price, imgSrc) {
    document.getElementById('modalTitle').innerText = name;
    document.getElementById('modalProductName').value = name;
    document.getElementById('modalProductPrice').value = price;
    document.getElementById('modalPrice').innerText = price + 'đ';
    document.querySelector('.modal-img').src = imgSrc;
    document.getElementById('cartModal').style.display = 'block';
}

// Đóng modal
function closeCartModal() {
    document.getElementById('cartModal').style.display = 'none';
}

// Chọn phân loại
function selectOption(btn) {
    document.querySelectorAll('.option-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    document.getElementById('modalProductOption').value = btn.innerText;
}

// Tăng số lượng
function increaseQty() {
    let qty = parseInt(document.getElementById('qtyInput').value);
    qty++;
    document.getElementById('qtyInput').value = qty;
    document.getElementById('modalProductQty').value = qty;
}

// Giảm số lượng
function decreaseQty() {
    let qty = parseInt(document.getElementById('qtyInput').value);
    if (qty > 1) {
        qty--;
        document.getElementById('qtyInput').value = qty;
        document.getElementById('modalProductQty').value = qty;
    }
}

// Đóng modal khi click ra ngoài
window.onclick = function (event) {
    const modal = document.getElementById('cartModal');
    if (event.target === modal) modal.style.display = "none";
};

// Gán sự kiện cho tất cả nút "Thêm vào giỏ hàng"
document.querySelectorAll('.add-to-cart').forEach(btn => {
    btn.addEventListener('click', function () {
        const productCard = btn.closest('.product-card');
        const name = productCard.querySelector('.product-title').innerText;
        const price = productCard.querySelector('.new-price').innerText.replace('đ', '').trim();
        const img = productCard.querySelector('img').src;

        openCartModal(name, price, img);
    });
});





const productVariants = {
    p1: [
        { name: '130g trắng', img: 'assets/images/hatomogi_trang.jpg', price: 40000 },
        { name: '130g xanh', img: 'assets/images/hatomogi_xanh.jpg', price: 40000 }
    ],
    p2: [
        { name: '88ml', img: 'assets/images/product2.jpg', price: 88000 },
        { name: '236ml', img: 'assets/images/product2.jpg', price: 284000 }
    ],
    p3: [
        { name: 'Vàng', img: 'assets/images/p31.jpg', price: 295000 },
        { name: 'Đen', img: 'assets/images/p32.jpg', price: 295000 },
        { name: 'Trắng', img: 'assets/images/p33.jpg', price: 295000 }
    ],
    p4: [
        { name: 'A01#', img: 'assets/images/p41.jpg', price: 36300 },
        { name: 'A02#', img: 'assets/images/p42.jpg', price: 39600 }
    ],
    p5: [
        { name: '10ml', img: 'assets/images/p5.jpg', price: 311000 },
        { name: '30ml', img: 'assets/images/p5.jpg', price: 651000 }
    ],
    p6: [
        { name: 'JUICY 20 + Glas 16', img: 'assets/images/p61.jpg', price: 269000 },
        { name: 'JUICY 23 + Glas 16', img: 'assets/images/p62.jpg', price: 269000 }
    ],
    p7: [
        { name: '60ml', img: 'assets/images/p7.jpg', price: 65600 }
    ],
    p8: [
        { name: 'Sạch da giảm nhờn', img: 'assets/images/p81.jpg', price: 254000 },
        { name: 'Dịu nhẹ da nhạy', img: 'assets/images/p82.jpg', price: 254000 },
        { name: 'Dành cho da sạm', img: 'assets/images/p83.jpg', price: 254000 }
    ],
    p9: [
        { name: 'NTT + SRM', img: 'assets/images/p9.jpg', price: 165000 },
        { name: 'NTT + KD', img: 'assets/images/p9.jpg', price: 120000 },
        { name: 'SRM + KD', img: 'assets/images/p9.jpg', price: 139000 },
        { name: 'NTT + TONER + SRm + KD', img: 'assets/images/p9.jpg', price: 304000 }
    ],
    p10: [
        { name: 'Taupe', img: 'assets/images/p101.jpg', price: 264000 },
        { name: 'Best ever', img: 'assets/images/p102.jpg', price: 264000 }
    ]
};

function openCartModal(productId, name) {
    const variants = productVariants[productId];
    const defaultVariant = variants[0];

    document.getElementById('modalTitle').innerText = name;
    document.getElementById('modalProductName').value = name;
    document.getElementById('modalProductQty').value = 1;
    document.getElementById('qtyInput').value = 1;

    // Render ảnh và giá mặc định
    document.querySelector('.modal-img').src = defaultVariant.img;
    document.getElementById('modalProductPrice').value = defaultVariant.price;
    document.getElementById('modalPrice').innerText = defaultVariant.price + 'đ';
    document.getElementById('modalProductOption').value = defaultVariant.name;

    // Render danh sách phân loại
    const optionGroup = document.querySelector('.option-btn-group');
    optionGroup.innerHTML = ''; // Clear cũ

    variants.forEach((variant, index) => {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'option-btn' + (index === 0 ? ' active' : '');
        btn.innerText = variant.name;
        btn.setAttribute('data-img', variant.img);
        btn.setAttribute('data-price', variant.price);
        btn.onclick = function () {
            selectOption(this);
        };
        optionGroup.appendChild(btn);
    });

    document.getElementById('cartModal').style.display = 'block';
}

// Chọn phân loại
function selectOption(btn) {
    document.querySelectorAll('.option-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');

    document.querySelector('.modal-img').src = btn.getAttribute('data-img');
    const price = btn.getAttribute('data-price');
    document.getElementById('modalPrice').innerText = price + 'đ';
    document.getElementById('modalProductPrice').value = price;
    document.getElementById('modalProductOption').value = btn.innerText;
}

// Đóng modal
function closeCartModal() {
    document.getElementById('cartModal').style.display = 'none';
}

// Tăng / giảm số lượng
function increaseQty() {
    let qty = parseInt(document.getElementById('qtyInput').value);
    qty++;
    document.getElementById('qtyInput').value = qty;
    document.getElementById('modalProductQty').value = qty;
}

function decreaseQty() {
    let qty = parseInt(document.getElementById('qtyInput').value);
    if (qty > 1) {
        qty--;
        document.getElementById('qtyInput').value = qty;
        document.getElementById('modalProductQty').value = qty;
    }
}

// Đóng khi click ngoài
window.onclick = function (event) {
    const modal = document.getElementById('cartModal');
    if (event.target === modal) modal.style.display = "none";
};

// Gán sự kiện nút
document.querySelectorAll('.add-to-cart').forEach(btn => {
    btn.addEventListener('click', function () {
        const productCard = btn.closest('.product-card');
        const id = productCard.dataset.id;
        const name = productCard.querySelector('.product-title').innerText;
        openCartModal(id, name);
    });
});