<template>
    <div :id="'reply'+id" class="panel panel-default">
        <div class="panel-heading">
            <div class="level">
                <h5 class="flex">
                    <a :href="'/profiles/'+reply.owner.name"
                        v-text="reply.owner.name">
                    </a> 回复于 {{ reply.created_at }}
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

            <div v-else v-text="body"> </div>
        </div>

        <div class="panel-footer level">
            <button class="btn btn-xs mr-1" @click="editing = true">Edit</button>
            <button class="btn btn-xs btn-danger mr-1" @click="destroy">Delete</button>
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
              body: this.reply.body
            };
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
            }
        }
    }
</script>