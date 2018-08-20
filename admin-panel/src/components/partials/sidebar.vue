<template>
  <div>
    <ul class="navbar-nav navbar-sidenav" id="exampleAccordion">

      <!-- dashboard link -->
      <li class="nav-item" data-toggle="tooltip" data-placement="right" title="Dashboard">
        <router-link class="nav-link" to="/dashboard">
            <font-awesome-icon icon="tachometer-alt" />
            <span class="nav-link-text">Dashboard</span>
        </router-link>
      </li>

      <!-- categories link -->
      <li class="nav-item" data-toggle="tooltip" data-placement="right" title="Categories">
        <router-link class="nav-link" to="/categories">
          <font-awesome-icon icon="cube" />
          <span class="nav-link-text">Categories</span>
        </router-link>
      </li>

      <!-- posts links -->
      <li class="nav-item" data-toggle="tooltip" data-placement="right" title="Posts">
        <a class="nav-link nav-link-collapse collapsed" data-toggle="collapse" href="#post-btn" data-parent="#exampleAccordion">
          <font-awesome-icon  :icon="['far', 'clone']" />
          <span class="nav-link-text">Posts</span>
          <font-awesome-icon class="angle-collapse" icon="angle-right" />
        </a>
        <ul class="sidenav-second-level collapse" id="post-btn">
          <li>
            <router-link to="/posts/new-post">New Post</router-link>
          </li>
          <li>
            <router-link to="/posts/unpublished">Unpublished Posts</router-link>
          </li>
          <li>
            <router-link :to="'/users/' + user + '/posts'">My Posts</router-link>
          </li>
          <li>
            <router-link to="/posts" exact>All posts</router-link>
          </li>
        </ul>
      </li>

      <!-- tags link -->
      <li class="nav-item" data-toggle="tooltip" data-placement="right" title="Tags">
        <router-link to="/tags" class="nav-link">
          <font-awesome-icon icon="tag" />
          <span class="nav-link-text">Tags</span>
        </router-link>
      </li>

      <!-- comment link -->
      <li class="nav-item" data-toggle="tooltip" data-placement="right" title="Latest Comments">
        <router-link to="/comments" class="nav-link">
          <font-awesome-icon icon="comments" />
          <span class="nav-link-text">Latest Comments</span>
        </router-link>
      </li>

      <!-- users links -->
      <li class="nav-item" data-toggle="tooltip" data-placement="right" title="Users">
        <a class="nav-link nav-link-collapse collapsed" data-toggle="collapse" href="#collapseMulti" data-parent="#exampleAccordion">
          <font-awesome-icon :icon="['far', 'user']" />
          <span class="nav-link-text">Users</span>
          <font-awesome-icon class="angle-collapse" icon="angle-right" />
        </a>
        <ul class="sidenav-second-level collapse" id="collapseMulti">
          <li>
            <router-link to="/users/banned">Banned Users</router-link>
          </li>
          <li>
            <router-link to="/users" exact>All Users</router-link>
          </li>
        </ul>
      </li>

      <!-- settings link -->
      <li class="nav-item" data-toggle="tooltip" data-placement="right" title="Settings">
        <a class="nav-link" href="settings.html">
          <font-awesome-icon icon="wrench" />
          <span class="nav-link-text">Settings</span>
        </a>
      </li>
    </ul>

    <!-- sidebar toggler btn -->
    <ul class="navbar-nav sidenav-toggler">
      <li class="nav-item">
        <a class="nav-link text-center" id="sidenavToggler">
          <font-awesome-icon icon="angle-left" />
        </a>
      </li>
    </ul>
    <!-- /sidebar toggler btn -->
  </div>
</template>
<script>
export default {
  data: function () {
    return {
      user: window.Laravel.user.slug
    };
  }
}
</script>
