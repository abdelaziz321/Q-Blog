<template>
  <ul class="navbar-nav ml-auto">
    <li class="nav-item dropdown">

      <!-- messages button -->
      <a class="nav-link dropdown-toggle mr-lg-2" id="messagesDropdown" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <font-awesome-icon icon="envelope" />
        <font-awesome-icon size="sm" class="angle" icon="angle-down" />
        <span class="d-lg-none">Messages
          <span class="badge badge-pill badge-primary">12 New</span>
        </span>
        <span class="indicator text-primary d-none d-lg-block">
          <font-awesome-icon icon="circle" />
        </span>
      </a>

      <!-- messages dropdown -->
      <div class="dropdown-menu dropdown-menu-right" aria-labelledby="messagesDropdown">
        <h6 class="dropdown-header">New Messages:</h6>
        <div class="dropdown-divider"></div>
        <a class="dropdown-item" href="#">
          <strong>David Miller</strong>
          <span class="small float-right text-muted">11:21 AM</span>
          <div class="dropdown-message small">Hey there! This new version of SB Admin is pretty awesome! These messages clip off when they reach the end of the box so they don't overflow over to the sides!</div>
        </a>
        <div class="dropdown-divider"></div>
        <a class="dropdown-item" href="#">
          <strong>Jane Smith</strong>
          <span class="small float-right text-muted">11:21 AM</span>
          <div class="dropdown-message small">I was wondering if you could meet for an appointment at 3:00 instead of 4:00. Thanks!</div>
        </a>
        <div class="dropdown-divider"></div>
        <a class="dropdown-item" href="#">
          <strong>John Doe</strong>
          <span class="small float-right text-muted">11:21 AM</span>
          <div class="dropdown-message small">I've sent the final files over to you for review. When you're able to sign off of them let me know and we can discuss distribution.</div>
        </a>
        <div class="dropdown-divider"></div>
        <a class="dropdown-item small" href="#">View all messages</a>
      </div>
    </li>

    <!-- logout button -->
    <li class="nav-item">
      <a class="nav-link logout" @click.prevent="logout">
        <font-awesome-icon icon="sign-out-alt" size="sm" />
        Logout
      </a>
    </li>

  </ul>
</template>
<script>
export default {
  methods: {
    logout() {
      this.$auth.logout({
        makeRequest: true,
        data: {},
        success: function () {},
        error: function () {},
        redirect: '/login'
      });
    }
  }
}
</script>
