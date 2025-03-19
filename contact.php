<?php require('Assets/head.php') ?>
<?php require('Assets/navbar.php') ?>

<section class="home-slider owl-carousel">
  <div class="slider-item bread-item" style="background-image: url('Assets/images/bg_1.jpg');" data-stellar-background-ratio="0.5">
    <div class="overlay"></div>
    <div class="container" data-scrollax-parent="true">
      <div class="row slider-text align-items-end">
        <div class="col-md-7 col-sm-12 ftco-animate mb-5">
          <p class="breadcrumbs" data-scrollax=" properties: { translateY: '70%', opacity: 1.6}"><span class="mr-2"><a href="index.php">Home</a></span> <span>Contact Us</span></p>
          <h1 class="mb-3" data-scrollax=" properties: { translateY: '70%', opacity: .9}">Get In Touch With Us</h1>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="ftco-section contact-section ftco-degree-bg" style="background-color: #f8f9fa;">
  <div class="container">
    <div class="row d-flex mb-5 contact-info">
      <div class="col-md-12 mb-4">
        <h2 class="h4 text-center text-primary">Contact Information</h2>
        <p class="text-center">We're here to help. Reach out to us using the contact details below.</p>
      </div>
      <div class="w-100"></div>
      <div class="col-md-3">
        <p><span class="font-weight-bold">Address:</span> 198 West 21st Street, Suite 721, New York, NY 10016</p>
      </div>
      <div class="col-md-3">
        <p><span class="font-weight-bold">Phone:</span> <a href="tel://1234567920" class="text-decoration-none text-dark">+ 1235 2355 98</a></p>
      </div>
      <div class="col-md-3">
        <p><span class="font-weight-bold">Email:</span> <a href="mailto:info@yoursite.com" class="text-decoration-none text-dark">info@yoursite.com</a></p>
      </div>
      <div class="col-md-3">
        <p><span class="font-weight-bold">Website:</span> <a href="#" class="text-decoration-none text-dark">yoursite.com</a></p>
      </div>
    </div>

    <div class="row block-9">
      <div class="col-md-12 pr-md-5">
        <form action="#" method="post" class="bg-light p-4 rounded shadow-sm">
          <div class="form-group">
            <input type="text" class="form-control" placeholder="Your Name" required>
          </div>
          <div class="form-group">
            <input type="email" class="form-control" placeholder="Your Email" required>
          </div>
          <div class="form-group">
            <input type="text" class="form-control" placeholder="Subject" required>
          </div>
          <div class="form-group">
            <textarea name="message" id="message" cols="30" rows="7" class="form-control" placeholder="Your Message" required></textarea>
          </div>
          <div class="form-group text-center">
            <input type="submit" value="Send Message" class="btn btn-primary py-3 px-5 mt-3">
          </div>
        </form>
      </div>
    </div>
  </div>
</section>

<?php require('Assets/foot.php') ?>

<?php require('Assets/footer.php') ?>
