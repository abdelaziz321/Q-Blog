<template>
  <q-layout view="hhh lpr fff">
		<!-- ============= header ============= -->
		<blog-header />

		<!-- ============= content ============= -->
    <q-page-container>
      <router-view />
    </q-page-container>

		<!-- ============= footer ============= -->
		<blog-footer />
  </q-layout>
</template>

<script>
import BlogHeader from 'pages/partials/header';
import BlogFooter from 'pages/partials/footer';

export default {
	components: {
		BlogHeader,
		BlogFooter
	}
}
</script>