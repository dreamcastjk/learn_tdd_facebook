<template>
    <div class="flex flex-col items-center">
        <p v-if="userLoading">User loading...</p>

        <div v-else class="relative mb-8">
            <div class="w-100 h-64 overflow-hidden">
                <img class="object-cover w-full" src="https://images.unsplash.com/photo-1612151855475-877969f4a6cc?ixid=MnwxMjA3fDB8MHxzZWFyY2h8MXx8aGQlMjBpbWFnZXxlbnwwfHwwfHw%3D&ixlib=rb-1.2.1&w=1000&q=80" alt="users avatar">
            </div>

            <div class="absolute flex items-center bottom-0 left-0 -mb-8 ml-12 z-20">
                <div class="w-32">
                    <img class="object-cover w-32 h-32 border-4 border-gray-200 rounded-full shadow-lg" src="https://sun9-79.userapi.com/impg/wxyHa5uJKvMR7M_9yUkJ1qf_XNvqe7sTAngCkg/UBKZyI0qMeQ.jpg?size=1080x716&quality=96&sign=aa02f491b34630242011f9daa329a95a&type=album" alt="user profile image">
                </div>

                <p class="ml-4 text-2xl text-gray-100 ml-4">{{ user.data.attributes.name }}</p>
            </div>

            <div class="absolute flex items-center bottom-0 right-0 mb-4 mr-12 z-20">
                <button class="py-1 px-3 bg-gray-400 rounded">Add Friend</button>
            </div>

        </div>

        <p v-if="postLoading">Posts loading...</p>

        <Post v-else v-for="post in posts.data" :key="post.data.post_id" :post="post" />

        <p v-if=" ! postLoading && posts.data.length < 1">No posts found. Get started...</p>
    </div>
</template>

<script>

import Post from '../../components/Post';
import { mapGetters } from 'vuex';

export default {
    name: "Show",

    components: {
        Post
    },

    data: () => {
        return {
            posts: null,
            postLoading: true,
        }
    },

    mounted() {
        this.$store.dispatch('fetchUser', this.$route.params.userId);

        axios.get('/api/users/' + this.$route.params.userId + '/posts')
            .then(res => {
                this.posts = res.data;
            })
            .catch(error => {
                console.log('Unable to fetch posts');
            })
            .finally(() => {
                this.postLoading = false;
            });
    },

    computed: {
        ...mapGetters({
            user: 'user',
        })
    }
}
</script>

<style scoped>

</style>
