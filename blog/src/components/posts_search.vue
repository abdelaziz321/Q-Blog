<template>
	<form class="search_form" @submit.prevent>
		<!-- search using title && search button -->
		<div class="main_buttons row q-mb-md">
			<q-btn
				class="self-end"
				label="Advanced"
				no-caps
				color="primary"
				@click="toggleCollapsible"
			/>

			<q-input
				v-model="title"
				float-label="the title of the post"
				class="col q-mx-md"
				color="primary"
			>
			</q-input>

			<q-btn
				class="self-end"
				icon="search"
				color="primary"
				@click="search"
			/>
		</div>

		<q-collapsible
			ref="collapsible"
			class="advanced_search bg-white q-mb-xl"
			header-class="header"
		>
			<div class="q-py-md">
				<!-- ======== Search fields[category-author-tags] ======== -->
				<div class="row q-mb-md">
					<q-toggle
						v-model="categoryToggle"
						no-focus
						color="primary"
						class="self-end col-xl-2 col-3"
						label="Categories"
					/>
					<q-search
						v-model="category"
						class="col q-ml-md"
						no-icon
						:debounce="600"
						placeholder="category title"
						@focus="categoryToggle = true"
					>
						<q-autocomplete
							@search="searchCategories"
							value-field="slug"
						/>
					</q-search>
				</div>

				<div class="row q-mb-md">
					<q-toggle
						v-model="authorToggle"
						no-focus
						color="primary"
						class="self-end col-xl-2 col-3"
						label="Authors"
					/>
					<q-search
						v-model="author"
						class="col q-ml-md"
						no-icon
						:debounce="600"
						placeholder="author"
						@focus="authorToggle = true"
					>
						<q-autocomplete
							@search="searchAuthors"
							value-field="slug"
						/>
					</q-search>
				</div>

				<div class="row q-mb-md">
					<q-toggle
						v-model="tagsToggle"
						no-focus
						color="primary"
						class="self-end col-xl-2 col-3"
						label="Tags"
					/>

					<q-chips-input
						v-model="tags"
						class="col q-ml-md"
						:debounce="600"
						placeholder="tag name"
						@focus="tagsToggle = true"
					>
					  <q-autocomplete
							@search="searchTags"
							value-field="slug"
						/>
					</q-chips-input>
				</div>

				<!-- ======== ACS && DESC toggles[recommends-views-comments-dates] ======== -->
				<div class="row q-mt-xl">
					<!-- recommends -->
					<div class="col-sm-6 q-mb-lg">
						<q-toggle
							no-focus
							@input="recomms = recommsToggle ? 'DESC' : ''"
							color="primary"
							v-model="recommsToggle"
							label="Recomms"
						/>

						<q-btn-toggle
							size="sm"
							class="q-ml-md"
							v-model="recomms"
							@input="recommsToggle = true"
							toggle-color="primary"
							:options="[
								{label: 'ASC', value: 'ASC'},
								{label: 'DESC', value: 'DESC'},
							]"
						/>
					</div>

					<!-- views -->
					<div class="col-sm-6 q-mb-lg">
						<q-toggle
							v-model="viewsToggle"
							no-focus
							@input="views = viewsToggle ? 'DESC' : ''"
							color="primary"
							label="Views"
						/>

						<q-btn-toggle
							v-model="views"
							size="sm"
							class="q-ml-md"
							@input="viewsToggle = true"
							toggle-color="primary"
							:options="[
								{label: 'ASC', value: 'ASC'},
								{label: 'DESC', value: 'DESC'},
							]"
						/>
					</div>

					<!-- comments -->
					<div class="col-sm-6 q-mb-lg">
						<q-toggle
							v-model="commentsToggle"
							no-focus
							@input="comments = commentsToggle ? 'DESC' : ''"
							color="primary"
							label="Comments"
						/>

						<q-btn-toggle
							v-model="comments"
							size="sm"
							class="q-ml-md"
							@input="commentsToggle = true"
							toggle-color="primary"
							:options="[
								{label: 'ASC', value: 'ASC'},
								{label: 'DESC', value: 'DESC'},
							]"
						/>
					</div>

					<!-- dates -->
					<div class="col-sm-6 q-mb-lg">
						<q-toggle
							v-model="dateToggle"
							no-focus
							@input="date = dateToggle ? 'DESC' : ''"
							color="primary"
							label="Date:"
						/>

						<q-btn-toggle
							v-model="date"
							size="sm"
							class="q-ml-md"
							@input="dateToggle = true"
							toggle-color="primary"
							:options="[
								{label: 'ASC', value: 'ASC'},
								{label: 'DESC', value: 'DESC'},
							]"
						/>
					</div>
				</div>
			</div>
		</q-collapsible>
	</form>
</template>

<script>
export default {
	data: function () {
		return {
			dateToggle: false,
			tagsToggle: false,
			viewsToggle: false,
			authorToggle: false,
			recommsToggle: false,
			categoryToggle: false,
			commentsToggle: false,

			tags: [],
			date: '',
			views: '',
			title: '',
			author: '',
			recomms: '',
			category: '',
			comments: '',
		};
	},


	watch: {
		commentsToggle: function (val) {
			if (val === false) {
				this.comments = '';
			}
		},
		recommsToggle: function (val) {
			if (val === false) {
				this.recomms = '';
			}
		},
		viewsToggle: function (val) {
			if (val === false) {
				this.views = '';
			}
		},
		dateToggle: function (val) {
			if (val === false) {
				this.date = '';
			}
		},
  },


	methods: {
		toggleCollapsible() {
			this.$refs.collapsible.toggle();
    },

		searchCategories(category, done) {
			this.axios.get('/categories/search?q=' + category)
			.then((response) => {
				response.data.forEach((item) => {
					item.label = item.title;
				});
				done(response.data)
			})
			.catch((errors) => {
				console.log(errors);
				done([]);
			});
		},

		searchAuthors(author, done) {
			this.axios.get('/users/search?q=' + author)
			.then((response) => {
				response.data.forEach((item) => {
					item.label = item.username;

					let avatarPath = this.$baseURL + '/storage/users/';
					if (item.avatar === null) {
						item.avatar = avatarPath + 'avatar.svg';
					} else {
						item.avatar = avatarPath + item.avatar;
					}
				});
				done(response.data)
			})
			.catch((errors) => {
				console.log(errors);
				done([]);
			});
		},

		searchTags(tag, done) {
			this.axios.get('/tags/search?q=' + tag)
			.then((response) => {
				response.data.forEach((item) => {
					item.label = item.name;
				});
				done(response.data)
			})
			.catch((errors) => {
				console.log(errors);
				done([]);
			});
		},

		search() {
			let query = '';

			if (this.title !== '') {
				query += 'title=' + this.title + '&';
			}
			if (this.categoryToggle && this.category !== '') {
				query += 'category=' + this.category + '&';
			}
			if (this.authorToggle && this.author !== '') {
				query += 'author=' + this.author + '&';
			}
			if (this.tagsToggle && this.tags.length !== 0) {
				this.tags.forEach((tag) => {
					query += 'tags[]=' + tag + '&';
				});
			}
			if (this.recommsToggle) {
				query += 'recommendations=' + this.recomms + '&';
			}
			if (this.commentsToggle) {
				query += 'comments=' + this.comments + '&';
			}
			if (this.viewsToggle) {
				query += 'views=' + this.views + '&';
			}
			if (this.dateToggle) {
				query += 'date=' + this.date + '&';
			}
			if (query.charAt(query.length - 1) === '&') {
				query = query.slice(0, query.length - 1);
			}

			this.$emit('search', query);
		}
	}
}
</script>
