<template>
  <div>
    <!-- Navigation-->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top" id="mainNav">
      <router-link class="navbar-brand text-center" to="/">
        <font-awesome-icon icon="book" />
        Q-Blog
        <span>Admin Panel</span>
      </router-link>
      <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarResponsive">
        <sidebar />
        <navbar />
      </div>
    </nav>

    <!-- notifications message -->
    <message />

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

    <chat v-if="chatOpenned" />

    <div v-else class="chat text-secondary hide">
      <h4 class="header" @click="chatOpenned = true">
        <font-awesome-icon icon="globe" />
        chat with authors
      </h4>
    </div>

    <!-- form modal -->
    <modal></modal>
  </div>
</template>
<script>
import Navbar from './partials/navbar';
import Sidebar from './partials/sidebar';
import Foooter from './partials/footer';
import Modal from './partials/notifications/modal';
import Message from './partials/notifications/message';
import Chat from '@/components/chat';


export default {
  components: {
    Navbar, Sidebar, Foooter, Modal, Message, Chat
  },


  data: function () {
    return {
      chatOpenned: false
    }
  },


  created: function () {
    window.$user = this.$auth.user();
    this.$gate.setUser(window.$user);
  }
}
</script>
