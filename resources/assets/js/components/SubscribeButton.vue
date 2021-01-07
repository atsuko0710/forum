<template>
    <button :class="classes" @click="subscribe">Subscribe</button>
</template>

<script>
    export default {
        props: ['active'],

        computed: {
            classes() {
                return ['btn', this.active ? 'btn-primary' : 'btn-default'];
            }
        },

        methods:{
            subscribe(){
                // axios.post(location.pathname + '/subscriptions');

                axios[
                    (this.active) ? 'delete' : 'post'
                ](location.pathname + '/subscriptions');

                // flash('Subscribed');
                this.active = ! this.active;
            }
        }
    }
</script>