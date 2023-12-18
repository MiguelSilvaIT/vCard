const httpServer = require("http").createServer();
const io = require("socket.io")(httpServer, {
  cors: {
    origin: "*",
    methods: ["GET", "POST"],
    credentials: true,
  },
});
httpServer.listen(8080, () => {
  console.log("listening on *:8080");
});

io.on("connection", (socket) => {
  console.log(`client ${socket.id} has connected`);

  socket.on("echo", (message) => {
    socket.emit("echo", message);
  });

  socket.on('loggedIn', function (user) {
    socket.join(user.id.toString())
    if (user.type == 'A') {
      socket.join('administrator')
    }
  })
  socket.on('loggedOut', function (user) {
    socket.leave(user.id)
    socket.leave('administrator')
  })
  socket.on('newTransaction', function (transaction) {
    socket.in(transaction.pair_vcard).emit('newTransaction', transaction);
  })
  socket.on('blockedUser', function (user) {
    socket.in(user.phone_number).emit('blockedUser', user);
  })
  socket.on('max_debit', function (user) {
    socket.in(user.phone_number).emit('max_debit', user);
  })
  socket.on('deletedUser', function (user) {
      console.log(user)
      socket.in(user.phone_number).emit('deletedUser', user);
    })

});