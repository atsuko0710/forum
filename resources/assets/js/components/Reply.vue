<template>
    <div :id="'reply'+id" class="panel panel-default">
        <div class="panel-heading">
            <div class="level">
                <h5 class="flex">
                    <a :href="'/profiles/'+reply.owner.name"
                        v-text="reply.owner.name">
                    </a> 回复于 <span v-text="ago"></span>
                </h5>

                <div>
                    <favorite :reply="reply"></favorite>
                </div>
            </div>
        </div>

        <div class="panel-body">
            <div v-if="editing">
                <div class="form-group">
                    <textarea class="form-control" v-model="body"></textarea>
                </div>

                <button class="btn btn-xs btn-primary" @click="update">Update</button>
                <button class="btn btn-xs btn-link" @click="editing = false">Cancel</button>
            </div>

            <div v-else v-html="body"> </div>
        </div>

        <div class="panel-footer level">
            <div v-if="authorize('updateReply',reply)">
                <button class="btn btn-xs mr-1" @click="editing = true">Edit</button>
                <button class="btn btn-xs btn-danger mr-1" @click="destroy">Delete</button>
            </div>

            <button class="btn btn-xs btn-default ml-a" @click="markBestReply" v-show="!isBest">Best Reply</button>
        </div>
    </div>
</template>
<script>
    import Favorite from './Favorite.vue';
    import moment from 'moment';

    export default {
        props: ['reply'],

        components: { Favorite },

        data() {
            return {
              editing: false,
              id: this.reply.id,
              body: this.reply.body,
              isBest: false
            };
        },

        computed : {
            ago() {
                return moment(this.reply.created_at).fromNow() + '...';
            }
        },

        created() {
            window.event.$on('best-reply-selected', id => {
                this.isBest = (id === this.id)
            });
        },

        methods:{
            update() {
                axios.patch('/replies/' + this.reply.id,{
                    body:this.body
                });

                this.editing = false;

                flash('成功更新回复');
            },

            destroy() {
                axios.delete('/replies/' + this.reply.id);

                this.$emit('deleted', this.reply.id)
            },

            markBestReply() {
                this.isBest = true;

                axios.post('/replies/' + this.data.id + '/best');

                window.events.$emit('best-reply-selected', this.data.id);
            }
        }
    }
</script>