<div class="checkout-screen bg-[#F8F9FA] p-[20px] rounded-[16px] min-h-screen">
    {{-- Header --}}
    <div class="navbar flex justify-between items-center mb-[20px]">
        <button class="back-button bg-white rounded-[24px] py-[8px] px-[16px] text-[14px] font-[500]">← Menu</button>
        <div class="user-profile flex items-center justify-end">
            <img src="https://example.com/avatar/khushboo.jpg" alt="User Avatar" class="w-[32px] h-[32px] rounded-full">
            <span class="username text-[14px] font-[600] ml-[8px]">Khushboo</span>
            {{-- Assuming a simple settings icon, can be replaced with an actual SVG/FontAwesome icon --}}
            <svg class="w-[20px] h-[20px] text-[#666] ml-[8px]" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
        </div>
    </div>

    {{-- Customer Info Banner --}}
    <div class="customer-info bg-white rounded-[12px] p-[12px_16px] mb-[16px] flex items-center gap-[16px]">
        <img src="https://example.com/avatar/anup.jpg" alt="Customer Avatar" class="w-[32px] h-[32px] rounded-full">
        <span class="customer-name text-[14px] font-[600]">Anup kumar</span>
        <span class="phone-number text-[14px] text-[#666] ml-[16px]">7088706543</span>
        <span class="discount-badge bg-[#E0F7EA] text-[#00C853] py-[4px] px-[8px] rounded-[8px] text-[12px] ml-[16px]">15% Discount offer</span>
        <span class="customer-type bg-[#F1F3F4] text-[#555] py-[4px] px-[8px] rounded-[8px] text-[12px] ml-[16px]">Regular</span>
    </div>

    {{-- Main Content Grid --}}
    <div class="main-content grid grid-cols-2 gap-[20px]">
        {{-- Order Details Section --}}
        <div class="order-details-section bg-white rounded-[12px] p-[16px] shadow-[0_2px_8px_rgba(0,0,0,0.05)]">
            <h2 class="text-lg font-bold mb-[12px]">Order details</h2>
            <div class="order-table-header text-[12px] text-[#666] font-[500] pb-[8px] border-b border-[#EEE] grid grid-cols-4">
                <span>Dish name</span>
                <span>Add ons</span>
                <span>Quantity</span>
                <span class="text-right">Amounts</span>
            </div>
            <div class="order-items-list">
                {{-- Item 1 --}}
                <div class="flex justify-between items-center py-[12px] border-b border-[#F5F5F5]">
                    <span class="flex-1">Mexican tacos</span>
                    <span class="flex-1 text-[#666]">7 Delicious add ons</span>
                    <span class="w-[50px] text-center">2</span>
                    <span class="w-[80px] text-right">$12.77</span>
                </div>
                {{-- Item 2 --}}
                <div class="flex justify-between items-center py-[12px] border-b border-[#F5F5F5]">
                    <span class="flex-1">Submarine sandwich</span>
                    <span class="flex-1 text-[#666]">3 Delicious add ons</span>
                    <span class="w-[50px] text-center">2</span>
                    <span class="w-[80px] text-right">$19.46</span>
                </div>
                {{-- Item 3 --}}
                <div class="flex justify-between items-center py-[12px]">
                    <span class="flex-1">Garlic toast</span>
                    <span class="flex-1 text-[#666]">2 Delicious add ons</span>
                    <span class="w-[50px] text-center">2</span>
                    <span class="w-[80px] text-right">$8.69</span>
                </div>
            </div>
        </div>

        {{-- Payment Section --}}
        <div class="payment-section bg-white rounded-[12px] p-[16px] shadow-[0_2px_8px_rgba(0,0,0,0.05)]">
            <h2 class="text-lg font-bold mb-[4px]">Select payment mode</h2>
            <p class="text-[12px] text-[#666] mb-[16px]">Select a payment method that helps our customers to feel seamless experience during checkout</p>
            <div class="payment-options">
                {{-- Card Option --}}
                <div class="option-card bg-[#E0F7EA] border border-[#00C853] rounded-[12px] p-[12px_16px] mb-[8px] flex items-center">
                    {{-- Assuming simple icon placeholder --}}
                    <svg class="w-[24px] h-[24px] mr-[12px]" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 0 002 2z"></path></svg>
                    <div>
                        <h3 class="font-semibold">Pay using card</h3>
                        <p class="text-[12px] text-[#666]">Complete the payment using credit or debit card, using swipe machine</p>
                    </div>
                </div>
                {{-- Cash Option --}}
                <div class="option-card bg-white border border-[#DDD] rounded-[12px] p-[12px_16px] mb-[8px] flex items-center">
                    {{-- Assuming simple icon placeholder --}}
                    <svg class="w-[24px] h-[24px] mr-[12px]" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    <div>
                        <h3 class="font-semibold">Pay on cash</h3>
                        <p class="text-[12px] text-[#666]">Complete order payment using cash on hand from customers easy and simple</p>
                    </div>
                </div>
                {{-- UPI Option --}}
                <div class="option-card bg-white border border-[#DDD] rounded-[12px] p-[12px_16px] flex items-center">
                    {{-- Assuming simple icon placeholder --}}
                    <svg class="w-[24px] h-[24px] mr-[12px]" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M12 20a9 9 0 100-18 9 9 0 000 18z"></path></svg>
                    <div>
                        <h3 class="font-semibold">Pay using UPI or scan</h3>
                        <p class="text-[12px] text-[#666]">Ask customer to complete the payment using by scanning QR code or upi it</p>
                    </div>
                </div>
            </div>
            <button class="confirm-payment-button bg-[#00C853] text-white rounded-[24px] py-[12px] px-[24px] text-[16px] font-[600] mt-[20px] w-full text-center">Confirm payment</button>
        </div>
    </div>

    {{-- Bottom Section Grid --}}
    <div class="bottom-section grid grid-cols-2 gap-[20px] mt-[20px]">
        {{-- Discount Coupon Section --}}
        <div class="discount-coupon-section bg-white rounded-[12px] p-[16px] shadow-[0_2px_8px_rgba(0,0,0,0.05)]">
            <h2 class="text-lg font-bold mb-[12px]">Discount coupon</h2>
            <p class="coupon-description text-[12px] text-[#666] mb-[12px]">Here apply the offered discount coupons or customers provided coupons for special discount on current cart value.</p>
            <div class="flex items-center">
                <input type="text" placeholder="Enter coupon code here ex: 2XCFYD5" class="coupon-input border border-[#DDD] rounded-[8px] py-[8px] px-[12px] w-[70%] text-[14px]">
                <button class="apply-button bg-[#00C853] text-white rounded-[8px] py-[8px] px-[16px] text-[14px] ml-[8px]">Apply</button>
            </div>
        </div>

        {{-- Billing Summary --}}
        <div class="billing-summary bg-white rounded-[12px] p-[16px] shadow-[0_2px_8px_rgba(0,0,0,0.05)]">
            <div class="flex justify-between mb-[8px]">
                <span>Subtotal</span>
                <span>$40.92</span>
            </div>
            <div class="flex justify-between mb-[4px] text-[#666]">
                <span>Service charges</span>
                <span>+ $4.88</span>
            </div>
            <div class="flex justify-between mb-[4px] text-[#666]">
                <span>Restaurant tax</span>
                <span>+ $12.67</span>
            </div>
            <div class="flex justify-between mb-[12px] text-[#E53935]">
                <span>Special discount</span>
                <span>- $23.43</span>
            </div>
            <div class="flex justify-between font-[700] text-[16px] text-[#333]">
                <span>Total</span>
                <span>$35.04</span>
            </div>
        </div>
    </div>
</div>
