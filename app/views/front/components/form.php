<?php 
	$url = "http://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]; 
?>
<form novalidate id="email-form" class="mb-3 sm:mb-5 opacity-0 translate-y-3" data-contact-form method="post">
  <input type="hidden" name="item" value="emailform">
  <div class="flex flex-col space-y-3">
    <!-- Honeypot -->
    <input type="text" name="company" tabindex="-1" autocomplete="off" class="absolute left-[-9999px] opacity-0 bg-white"/>
    
    <input id="name" class="border border-white px-2 py-1 w-full opacity-0 translate-x-2 transition-all duration-500 ease-out" data-form-field data-delay="0" type="text" placeholder="Name" name="name" required>
    <input id="email" class="border border-white px-2 py-1 w-full opacity-0 translate-x-2 transition-all duration-500 ease-out" data-form-field data-delay="1" type="email" placeholder="Email" name="email" required>
    <textarea id="message" class="border border-white px-2 py-1 w-full opacity-0 translate-x-2 transition-all duration-500 ease-out" data-form-field data-delay="2" rows="5" placeholder="Message" name="message" required></textarea>
    
    <div class="form-group opacity-0 translate-y-2" data-form-button>
      <button id="formsub" class="bg-cyan-400 text-black px-2 py-1 mb-2 cursor-pointer hover:bg-cyan-500 disabled:opacity-50 transition-colors duration-200" type="submit">Submit</button>
    </div>
    
    <output id="result" class="border p-2 hidden"></output>
  </div>  
</form>