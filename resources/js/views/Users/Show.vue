<template>
    <div class="flex flex-col items-center" v-if="status.user === 'success' && user">
        <div class="relative mb-8">
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
                <button v-if="friendButtonText && friendButtonText !== 'Accept'"
                        class="py-1 px-3 bg-gray-400 rounded"
                        @click="$store.dispatch('sendFriendRequest', $route.params.userId)">
                    {{ friendButtonText }}
                </button>
                <button v-if="friendButtonText && friendButtonText === 'Accept'"
                        class="mr-2 py-1 px-3 bg-blue-500 rounded"
                        @click="$store.dispatch('acceptFriendRequest', $route.params.userId)">
                    Accept
                </button>
                <button v-if="friendButtonText && friendButtonText === 'Accept'"
                        class="py-1 px-3 bg-gray-400 rounded"
                        @click="$store.dispatch('ignoreFriendRequest', $route.params.userId)">
                    Ignore
                </button>
            </div>
        </div>

        <div v-if="status.posts === 'loading'">Posts loading...</div>

        <div v-else-if="posts.length < 1">No posts found. Get started...</div>

        <Post v-else v-for="post in posts.data" :key="post.data.post_id" :post="post" />
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

    mounted() {
        this.$store.dispatch('fetchUser', this.$route.params.userId);
        this.$store.dispatch('fetchUserPosts', this.$route.params.userId);
    },

    computed: {
        ...mapGetters({
            user: 'user',
            posts: 'posts',
            status: 'status',
            friendButtonText: 'friendButtonText',
        })
    }
}
</script>

<style scoped>

</style>
