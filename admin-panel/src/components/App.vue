<template>
  <div>
    <!-- Navigation-->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top" id="mainNav">
      <router-link class="navbar-brand text-center" to="/">Blog System <font-awesome-icon icon="book" /></router-link>
      <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarResponsive">
        <sidebar></sidebar>
        <navbar></navbar>
      </div>
    </nav>

    <!-- notifications message -->
    <message></message>

    <!-- main wrapper -->
    <div class="content-wrapper">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <router-view></router-view>
          </div>
        </div>
      </div>

      <foooter></foooter>
    </div>

    <!-- form modal -->
    <modal></modal>
  </div>
</template>
<script>
export default {
  created: function () {
    window.$user = this.$auth.user();
    this.$gate.setUser(window.$user);
  }
}
</script>
