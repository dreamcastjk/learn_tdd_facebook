<template>
    <div>
        <div class="w-100 h-64 overflow-hidden">
            <img class="object-cover w-full" src="https://static.remove.bg/remove-bg-web/207b10c4ce48e7dca1441ee119b7f52754f487fd/assets/start-0e837dcc57769db2306d8d659f53555feb500b3c5d456879b9c843d1872e7baa.jpg" alt="users avatar">
        </div>
    </div>
</template>

<script>
export default {
    name: "Show",

    data: () => {
        return {
            user: null,
            posts: [],
            loading: true,
        }
    },

    mounted() {
        axios.get('/api/users/' + this.$route.params.userId)
            .then(res => {
                this.user = res.data;
            })
            .catch(error => {
                console.log('Unable to fetch the user from the server.');
            })
            .finally(() => {
                this.loading = false;
            });

        axios.get('/api/posts' + this.$route.params.userId)
            .then(res => {
                this.posts = res.data;
            })
            .catch(error => {
                console.log('Unable to fetch posts');
            })
            .finally(() => {
                this.loading = false;
            });
    }
}
</script>

<style scoped>

</style>
