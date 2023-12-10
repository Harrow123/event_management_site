<div class="container mx-auto mt-8 p-4">
        <h1 class="text-3xl font-semibold mb-4">Contact Us</h1>
        <p class="mb-4">
            Have a question, feedback, or need assistance? We're here to help! Feel free to reach out to us using the contact information below, and our team will respond as soon as possible.
        </p>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <h2 class="text-xl font-semibold mb-2">Contact Information</h2>
                <p>Email: info@evently.com</p>
                <p>Phone: +1 (123) 456-7890</p>
                <p>Address: 123 Evently St, Kingston, Jamaica</p>
            </div>
            <div>
                <h2 class="text-xl font-semibold mb-2">Contact Form</h2>
                <form action="/contact" method="post">
                    <div class="mb-4">
                        <label class="block text-sm font-semibold" for="name">Name</label>
                        <input class="form-input" type="text" name="name" id="name" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-semibold" for="email">Email</label>
                        <input class="form-input" type="email" name="email" id="email" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-semibold" for="message">Message</label>
                        <textarea class="form-input" name="message" id="message" rows="4" required></textarea>
                    </div>
                    <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" type="submit">Submit</button>
                </form>
            </div>
        </div>
    </div>